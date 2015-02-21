<?php namespace LemonTree\Controllers;

use LemonTree\LoggedUser;
use LemonTree\UserActionType;
use LemonTree\Models\Group;
use LemonTree\Models\User;
use LemonTree\Models\UserAction;

class UserController extends Controller {

	public function getForm()
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		$groupList = Group::orderBy('name', 'asc')->get();

		$scope['groupList'] = $groupList;

		return \Response::json($scope);
	}

	public function getUser($id)
	{
		$scope = array();

		$user = User::find($id);

		if ( ! $user) {
			$scope['state'] = 'error_user_not_found';
			return \Response::json($scope);
		}

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		if (
			$loggedUser->id == $user->id
			|| $user->isSuperUser()
		) {
			$scope['state'] = 'error_user_access_denied';
			return \Response::json($scope);
		}

		$userGroups = $user->getGroups();

		$userGroupMap = array();

		foreach ($userGroups as $group) {
			$userGroupMap[$group->id] = true;
		}

		$user->groups = $userGroupMap;
		$user->isSuperUser = $user->isSuperUser();
		$user->password = null;

		$scope['user'] = $user;

		return \Response::json($scope);
	}

	public function delete($id)
	{
		$scope = array();

		$user = User::find($id);

		if ( ! $user) {
			$scope['state'] = 'error_user_not_found';
			return \Response::json($scope);
		}

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		if (
			$loggedUser->id == $user->id
			|| $user->isSuperUser()
		) {
			$scope['state'] = 'error_user_access_denied';
			return \Response::json($scope);
		}

		try {
			$user->delete();
		} catch (\Exception $e) {
			$scope['state'] = 'error_user_delete_failed';
			return \Response::json($scope);
		}

		UserAction::log(
			UserActionType::ACTION_TYPE_DROP_USER_ID,
			'ID '.$user->id.' ('.$user->login.')'
		);

		$scope['status'] = 'ok';

		return \Response::json($scope);
	}

	public function save($id = null)
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		$user = null;

		if ($id) {
			$user = User::find($id);

			if ( ! $user) {
				$scope['state'] = 'error_user_not_found';
				return \Response::json($scope);
			}

			if (
				$loggedUser->id == $user->id
				|| $user->isSuperUser()
			) {
				$scope['state'] = 'error_user_access_denied';
				return \Response::json($scope);
			}

			$actionType = UserActionType::ACTION_TYPE_SAVE_USER_ID;
		} else {
			$user = new User;

			$actionType = UserActionType::ACTION_TYPE_ADD_USER_ID;
		}

		$input = \Input::all();

		$rules = array(
			'login' => 'required',
			'email' => 'required|email',
			'first_name' => 'required',
			'last_name' => 'required',
		);

		$messages = array(
			'login.required' => 'Поле обязательно к заполнению',
			'email.required' => 'Поле обязательно к заполнению',
			'email' => 'Некорректный адрес электронной почты',
			'first_name.required' => 'Поле обязательно к заполнению',
			'last_name.required' => 'Поле обязательно к заполнению',
		);

		$groups = \Input::get('groups') ?: [];

		if (is_array($groups)) {
			foreach ($groups as $id => $value) {
				if ($value === true) {
					$input['group_'.$id] = $id;
					$rules['group_'.$id] = 'exists:cytrus_groups,id';
					$messages['group_'.$id.'.exists'] = 'Некорректный идентификатор';
				}
			}
		}

		$validator = \Validator::make($input, $rules, $messages);

		if ($validator->fails()) {
			$messages = $validator->messages()->getMessages();
			$errors = array();
			foreach ($messages as $field => $messageList) {
				foreach ($messageList as $message) {
					$errors[$field][] = $message;
				}
			}
			$scope['error'] = $errors;
			return json_encode($scope);
		}

		$user->login = \Input::get('login');
		if (\Input::get('password')) {
			$user->password = \Input::get('password');
		}
		$user->email = \Input::get('email');
		$user->first_name = \Input::get('first_name');
		$user->last_name = \Input::get('last_name');

		try {
			$user->save();
		} catch (\Exception $e) {
			$scope['state'] = 'error_user_save_failed';
			return \Response::json($scope);
		}

		$userGroups = $user->getGroups();

		$userGroupMap = array();

		foreach ($userGroups as $userGroup) {
			if (
				! isset($groups[$userGroup->id])
				|| $groups[$userGroup->id] !== true
			) {
				$user->removeGroup($userGroup);
			} else {
				$userGroupMap[$userGroup->id] = true;
			}
		}

		foreach ($groups as $id => $value) {
			if (
				$value === true
				&& ! isset($userGroupMap[$id])
			) {
				$group = Group::find($id);

				if ($group) {
					$user->addGroup($group);
					$userGroupMap[$group->id] = true;
				}
			}
		}

		$user->flush();

		$user->groups = $userGroupMap;

		UserAction::log(
			$actionType,
			'ID '.$user->id.' ('.$user->login.')'
		);

		$user->password = null;

		$scope['user'] = $user;
		$scope['status'] = 'ok';

		return \Response::json($scope);
	}

	public function getList()
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		$userList = User::orderBy('login', 'asc')->get();

		foreach ($userList as $user) {
			$user->groups = $user->getGroups();
			$user->isSuperUser = $user->isSuperUser();
		}

		$scope['userList'] = $userList;

		return \Response::json($scope);
	}

	public function getListByGroup($id)
	{
		$scope = array();

		$activeGroup = Group::find($id);

		if ( ! $activeGroup) {
			$scope['state'] = 'error_group_not_found';
			return \Response::json($scope);
		}

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		if ($loggedUser->inGroup($activeGroup)) {
			$scope['state'] = 'error_group_access_denied';
			return \Response::json($scope);
		}

		$userList = $activeGroup->users()->orderBy('login', 'asc')->get();

		foreach ($userList as $user) {
			$user->groups = $user->getGroups();
			$user->isSuperUser = $user->isSuperUser();
		}

		$scope['group'] = $activeGroup;
		$scope['userList'] = $userList;

		return \Response::json($scope);
	}

}
