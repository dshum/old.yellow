<?php namespace LemonTree\Controllers;

use LemonTree\LoggedUser;
use LemonTree\UserActionType;
use LemonTree\Models\UserAction;

class ProfileController extends Controller {

	public function save()
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		$input = \Input::all();

		$rules = array(
			'email' => 'required|email',
			'first_name' => 'required',
			'last_name' => 'required',
		);

		$messages = array(
			'email.required' => 'Поле обязательно к заполнению',
			'email' => 'Некорректный адрес электронной почты',
			'first_name.required' => 'Поле обязательно к заполнению',
			'last_name.required' => 'Поле обязательно к заполнению',
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

		$password = \Input::get('password');
		$email = \Input::get('email');
		$firstName = \Input::get('first_name');
		$lastName = \Input::get('last_name');

		if ($password) {
			$loggedUser->password = $password;
		}
		$loggedUser->email = $email;
		$loggedUser->first_name = $firstName;
		$loggedUser->last_name = $lastName;

		$loggedUser->save();

		UserAction::log(
			UserActionType::ACTION_TYPE_SAVE_PROFILE_ID,
			'ID '.$loggedUser->id.' ('.$loggedUser->login.')'
		);

		$scope['state'] = 'ok';

		return \Response::json($scope);
	}

}