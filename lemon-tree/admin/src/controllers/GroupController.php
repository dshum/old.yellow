<?php namespace LemonTree\Controllers;

use LemonTree\LoggedUser;
use LemonTree\UserActionType;
use LemonTree\Models\Group;
use LemonTree\Models\UserAction;
use LemonTree\Models\GroupItemPermission;
use LemonTree\Models\GroupElementPermission;

class GroupController extends Controller {

	public function getGroup($id)
	{
		$scope = array();

		$group = Group::find($id);

		if ( ! $group) {
			$scope['state'] = 'error_group_not_found';
			return \Response::json($scope);
		}

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		if ($loggedUser->inGroup($group)) {
			$scope['state'] = 'error_group_access_denied';
			return \Response::json($scope);
		}

		$group->admin = $group->hasAccess('admin');

		$scope['group'] = $group;

		return \Response::json($scope);
	}

	public function delete($id)
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		$group = Group::find($id);

		if ( ! $group) {
			$scope['state'] = 'error_group_not_found';
			return \Response::json($scope);
		}

		if ($loggedUser->inGroup($group)) {
			$scope['state'] = 'error_group_access_denied';
			return \Response::json($scope);
		}

		try {
			$group->delete();
		} catch (\Exception $e) {
			$scope['state'] = 'error_group_delete_failed';
			return \Response::json($scope);
		}

		UserAction::log(
			UserActionType::ACTION_TYPE_DROP_GROUP_ID,
			'ID '.$group->id.' ('.$group->name.')'
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

		$group = null;

		if ($id) {
			$group = Group::find($id);

			if ( ! $group) {
				$scope['state'] = 'error_group_not_found';
				return \Response::json($scope);
			}

			if ($loggedUser->inGroup($group)) {
				$scope['state'] = 'error_group_access_denied';
				return \Response::json($scope);
			}

			$actionType = UserActionType::ACTION_TYPE_SAVE_GROUP_ID;
		} else {
			$group = new Group;

			$actionType = UserActionType::ACTION_TYPE_ADD_GROUP_ID;
		}

		$input = \Input::all();

		$rules = array(
			'name' => 'required',
			'default_permission' => 'required|in:deny,view,update,delete',
		);

		$messages = array(
			'name.required' => 'Поле обязательно к заполнению',
			'default_permission.required' => 'Поле обязательно к заполнению',
			'default_permission.in' => 'Некорректное право доступа',
		);

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
			return \Response::json($scope);
		}

		$group->name = \Input::get('name');

		$group->default_permission = \Input::get('default_permission');

		$group->setPermission('admin', (boolean)\Input::get('admin'));

		try {

			$group->save();

			UserAction::log(
				$actionType,
				'ID '.$group->id.' ('.$group->name.')'
			);

		} catch (\Exception $e) {
			$scope['state'] = 'error_group_save_failed';
			return \Response::json($scope);
		}

		$group->admin = $group->hasAccess('admin');

		$scope['group'] = $group;
		$scope['status'] = 'ok';

		return \Response::json($scope);
	}

	public function postSaveItemPermissions($id)
	{
		$scope = array();

		$group = Group::find($id);

		if ( ! $group) {
			$scope['state'] = 'error_group_not_found';
			return \Response::json($scope);
		}

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		if ($loggedUser->inGroup($group)) {
			$scope['state'] = 'error_group_access_denied';
			return \Response::json($scope);
		}

		$input = \Input::all();

		$site = \App::make('site');

		$itemList = $site->getItemList();

		foreach ($itemList as $item) {
			$rules[$item->getName()] = 'required|in:deny,view,update,delete';
			$messages[$item->getName().'.required'] = 'Поле обязательно к заполнению';
			$messages[$item->getName().'.in'] = 'Некорректное право доступа';
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
			return \Response::json($scope);
		}

		$defaultPermission = $group->default_permission
			? $group->default_permission
			: 'deny';

		$itemPermissions = $group->itemPermissions;

		$permissionList = array();

		foreach ($itemPermissions as $itemPermission) {
			$class = $itemPermission->class;
			$permissionList[$class] = $itemPermission;
		}

		foreach ($itemList as $item) {

			$class = $item->getName();

			if (isset($permissionList[$class])) {

				$itemPermission = $permissionList[$class];

				$permission = $itemPermission->permission;

				if ($defaultPermission == $input[$class]) {
					$itemPermission->delete();
				} elseif ($permission != $input[$class]) {
					$itemPermission->permission = $input[$class];
					$itemPermission->save();
				}

			} elseif ($defaultPermission != $input[$class]) {

				$itemPermission = new GroupItemPermission;

				$itemPermission->group_id = $group->id;
				$itemPermission->class = $class;
				$itemPermission->permission = $input[$class];

				$itemPermission->save();

			}

		}

		UserAction::log(
			UserActionType::ACTION_TYPE_SAVE_ITEM_PERMISSIONS_ID,
			'ID '.$group->id.' ('.$group->name.')'
		);

		$scope['status'] = 'ok';

		return json_encode($scope);
	}

	public function postSaveElementPermissions($id)
	{
		$scope = array();

		$group = Group::find($id);

		if ( ! $group) {
			$scope['state'] = 'error_group_not_found';
			return \Response::json($scope);
		}

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		if ($loggedUser->inGroup($group)) {
			$scope['state'] = 'error_group_access_denied';
			return \Response::json($scope);
		}

		$input = \Input::all();

		$site = \App::make('site');

		$itemList = $site->getItemList();

		$itemElementList = array();

		foreach ($itemList as $itemName => $item) {
			if ( ! $item->getElementPermissions()) {
				unset($itemList[$itemName]);
				continue;
			}

			$elementList =
				$item->getClass()->
				orderBy($item->getMainProperty())->
				get();

			if ( ! sizeof ($elementList)) {
				unset($itemList[$itemName]);
				continue;
			}

			foreach ($elementList as $element) {
				$itemElementList[$itemName][$element->getClassId()] =
					$element->{$item->getMainProperty()};
			}
		}

		foreach ($itemElementList as $itemName => $elementList) {
			foreach ($elementList as $classId => $name) {
				$rules[$classId] = 'required|in:deny,view,update,delete';
				$messages[$classId.'.required'] = 'Поле обязательно к заполнению';
				$messages[$classId.'.in'] = 'Некорректное право доступа';
			}
		}

		$validator = \Validator::make($input, $rules, $messages);

		if ($validator->fails()) {
			$messages = $validator->messages()->getMessages();
			$scope['messages'] = $messages;
			$errors = array();
			foreach ($messages as $field => $messageList) {
				foreach ($messageList as $message) {
					$errors[$field][] = $message;
				}
			}
			$scope['error'] = $errors;
			return \Response::json($scope);
		}

		$defaultPermission = $group->default_permission
			? $group->default_permission
			: 'deny';

		$itemPermissions = $group->itemPermissions;

		$permissionList = array();

		foreach ($itemList as $itemName => $item) {
			$permissionList[$itemName] = $defaultPermission;
		}

		foreach ($itemPermissions as $itemPermission) {
			$class = $itemPermission->class;
			$permission = $itemPermission->permission;
			$permissionList[$class] = $permission;
		}

		$elementPermissions = $group->elementPermissions;

		foreach ($elementPermissions as $elementPermission) {
			$classId = $elementPermission->class_id;
			$permissionList[$classId] = $elementPermission;
		}

		foreach ($itemElementList as $itemName => $elementList) {

			$defaultItemPermission = isset($permissionList[$itemName])
				? $permissionList[$itemName] : $defaultPermission;

			foreach ($elementList as $classId => $name) {

				$value = \Input::get($classId);

				if (
					$defaultItemPermission !== $value
					&& ! isset($permissionList[$classId])
				) {
					$elementPermission = new GroupElementPermission;

					$elementPermission->group_id = $group->id;
					$elementPermission->class_id = $classId;
					$elementPermission->permission = $value;

					$elementPermission->save();
				} elseif (
					$defaultItemPermission !== $value
					&& isset($permissionList[$classId])
					&& $permissionList[$classId]->permission !== $value
				) {
					$elementPermission = $permissionList[$classId];

					$elementPermission->permission = $value;

					$elementPermission->save();
				} elseif (
					$defaultItemPermission === $value
					&& isset($permissionList[$classId])
				) {
					$elementPermission = $permissionList[$classId];

					$elementPermission->delete();
				}
			}

		}

		UserAction::log(
			UserActionType::ACTION_TYPE_SAVE_ELEMENT_PERMISSIONS_ID,
			'ID '.$group->id.' ('.$group->name.')'
		);

		$scope['status'] = 'ok';

		return json_encode($scope);
	}

	public function getElementPermissions($id)
	{
		$scope = array();

		$group = Group::find($id);

		if ( ! $group) {
			$scope['state'] = 'error_group_not_found';
			return \Response::json($scope);
		}

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		if ($loggedUser->inGroup($group)) {
			$scope['state'] = 'error_group_access_denied';
			return \Response::json($scope);
		}

		$site = \App::make('site');

		$items = $site->getItemList();

		$defaultPermission = $group->default_permission
			? $group->default_permission
			: 'deny';

		$itemPermissions = $group->itemPermissions;

		$permissionList = array();

		foreach ($itemPermissions as $itemPermission) {
			$class = $itemPermission->class;
			$permission = $itemPermission->permission;
			$permissionList[$class] = $permission;
		}

		$elementPermissions = $group->elementPermissions;

		foreach ($elementPermissions as $elementPermission) {
			$classId = $elementPermission->class_id;
			$permission = $elementPermission->permission;
			$permissionList[$classId] = $permission;
		}

		$itemList = array();
		$itemElementList = array();

		foreach ($items as $itemName => $item) {
			if ( ! $item->getElementPermissions()) {
				unset($items[$itemName]);
				continue;
			}

			$elementList =
				$item->getClass()->
				orderBy($item->getMainProperty())->
				get();

			if ( ! sizeof ($elementList)) {
				unset($items[$itemName]);
				continue;
			}

			$itemList[$itemName] = [
				'name' => $item->getName(),
				'title' => $item->getTitle(),
			];

			foreach ($elementList as $element) {
				$itemElementList[$itemName][$element->getClassId()] = [
					'classId' => $element->getClassId(),
					'name' => $element->{$item->getMainProperty()},
				];
			}
		}

		$scope['group'] = $group;
		$scope['itemList'] = $itemList;
		$scope['itemElementList'] = $itemElementList;
		$scope['permissionList'] = $permissionList;
		$scope['defaultPermission'] = $defaultPermission;

		return \Response::json($scope);
	}

	public function getItemPermissions($id)
	{
		$scope = array();

		$group = Group::find($id);

		if ( ! $group) {
			$scope['state'] = 'error_group_not_found';
			return \Response::json($scope);
		}

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasAccess('admin')) {
			$scope['state'] = 'error_admin_access_denied';
			return \Response::json($scope);
		}

		if ($loggedUser->inGroup($group)) {
			$scope['state'] = 'error_group_access_denied';
			return \Response::json($scope);
		}

		$site = \App::make('site');

		$items = $site->getItemList();

		$defaultPermission = $group->default_permission
			? $group->default_permission
			: 'deny';

		$itemPermissions = $group->itemPermissions;

		$itemList = array();

		foreach ($items as $item) {
			$itemList[$item->getName()] = [
				'name' => $item->getName(),
				'title' => $item->getTitle(),
			];
		}

		$permissionList = array();

		foreach ($itemPermissions as $itemPermission) {
			$class = $itemPermission->class;
			$permission = $itemPermission->permission;
			$permissionList[$class] = $permission;
		}

		$scope['group'] = $group;
		$scope['itemList'] = $itemList;
		$scope['defaultPermission'] = $defaultPermission;
		$scope['permissionList'] = $permissionList;

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

		$groupList = Group::orderBy('name', 'asc')->get();

		foreach ($groupList as $group) {
			$group->admin = $group->hasAccess('admin');
		}

		$scope['groupList'] = $groupList;

		return \Response::json($scope);
	}

}
