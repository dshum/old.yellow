<?php namespace LemonTree\Controllers;

use LemonTree\LoggedUser;
use LemonTree\UserActionType;
use LemonTree\Models\User;
use LemonTree\Models\UserAction;

class LoginController extends Controller {

	public function user()
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		$loggedUser->password = null;

		$scope['user'] = $loggedUser;

		return \Response::json($scope);
	}

	public function login()
	{
		$scope = array();

		$login = \Input::get('login');
		$password = \Input::get('password');

		if ( ! $login) {
			$scope['message'] = 'Введите логин.';
			return \Response::json($scope, 401);
		}

		if ( ! $password) {
			$scope['message'] = 'Введите пароль.';
			return \Response::json($scope, 401);
		}

		$user = User::where('login', $login)->first();

		if ( ! $user) {
			$scope['message'] = 'Неправильный логин или пароль.';
			return \Response::json($scope, 401);
		}

		if ($user->password != $password) {
			$scope['message'] = 'Неправильный логин или пароль.';
			return \Response::json($scope, 401);
		}

		if ($user->banned) {
			$scope['message'] = 'Пользователь заблокирован.';
			return \Response::json($scope, 401);
		}

		LoggedUser::setUser($user);

		UserAction::log(
			UserActionType::ACTION_TYPE_LOGIN_ID,
			$user->login
		);

		$secret = \Config::get('app.key');

		$payload = array(
            'user' => $user->id,
        );

		$scope['user'] = $user;
		$scope['token'] = \JWT::encode($payload, $secret);

		return \Response::json($scope);
	}

}