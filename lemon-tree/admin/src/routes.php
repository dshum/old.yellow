<?php

use LemonTree\LoggedUser;
use LemonTree\Models\User;

define('test', false);

Route::filter('admin.auth', function() {

	if (defined('test') && test === true) {

		$userId = 1;

	} else {
		if ( ! function_exists('apache_request_headers')) {
			function apache_request_headers() {
				$arh = array();
				$rx_http = '/\AHTTP_/';
				foreach ($_SERVER as $key => $val) {
					if (preg_match($rx_http, $key)) {
						$arh_key = preg_replace($rx_http, '', $key);
						$rx_matches = array();
						$rx_matches = explode('_', $arh_key);
						if (sizeof($rx_matches) > 0 and strlen($arh_key) > 2) {
							foreach ($rx_matches as $ak_key => $ak_val) {
								$rx_matches[$ak_key] = ucfirst($ak_val);
							}
							$arh_key = implode('-', $rx_matches);
						}
						$arh[$arh_key] = $val;
					}
				}
				return( $arh );
			}
		}

		$requestHeaders = apache_request_headers();

		$authorizationHeader = isset($requestHeaders['Authorization'])
			? $requestHeaders['Authorization'] : null;

		if ( ! $authorizationHeader) {
			$scope['message'] = "Заголовок авторизации не получен.";
			return Response::json($scope, 401);
		}

		$token = str_replace('Bearer ', '', $authorizationHeader);
		$secret = Config::get('app.key');
		$decoded_token = null;

		try {
			$decoded = JWT::decode($token, $secret);
		} catch(UnexpectedValueException $e) {
			$scope['message'] = "Недействительный токен.";
			return Response::json($scope, 401);
		}

		$userId = $decoded->user;

		if ( ! $userId) {
			$scope['message'] = "Идентификатор пользователя не получен.";
			return Response::json($scope, 401);
		}

	}

	if ( ! $user = User::find($userId)) {
		$scope['message'] = "Пользователь с идентификатором $userId не найден.";
		return Response::json($scope, 401);
	}

	LoggedUser::setUser($user);

});

Route::group(array('prefix' => 'admin'), function() {

	Route::get('/', 'LemonTree\Controllers\HomeController@getIndex');

});

Route::group(array('prefix' => 'api'), function() {

	Route::post('login', 'LemonTree\Controllers\LoginController@postLogin');

});

Route::group(array(
	'prefix' => 'api',
	'before' => 'admin.auth'
), function() {

	Route::get('user', 'LemonTree\Controllers\LoginController@getUser');

	Route::post('profile', 'LemonTree\Controllers\ProfileController@postSave');

	Route::post('group/add', 'LemonTree\Controllers\GroupController@save');

	Route::get('group/{id}', 'LemonTree\Controllers\GroupController@getGroup')->
		where('id', '[0-9]+');

	Route::post('group/{id}', 'LemonTree\Controllers\GroupController@save')->
		where('id', '[0-9]+');

	Route::delete('group/{id}', 'LemonTree\Controllers\GroupController@delete')->
		where('id', '[0-9]+');

	Route::get('group/{id}/items', 'LemonTree\Controllers\GroupController@getItemPermissions')->
		where('id', '[0-9]+');

	Route::post('group/{id}/items', 'LemonTree\Controllers\GroupController@postSaveItemPermissions')->
		where('id', '[0-9]+');

	Route::get('group/{id}/elements', 'LemonTree\Controllers\GroupController@getElementPermissions')->
		where('id', '[0-9]+');

	Route::post('group/{id}/elements', 'LemonTree\Controllers\GroupController@postSaveElementPermissions')->
		where('id', '[0-9]+');

	Route::get('group/list', 'LemonTree\Controllers\GroupController@getList');

	Route::get('user/form', 'LemonTree\Controllers\UserController@getForm');

	Route::post('user/add', 'LemonTree\Controllers\UserController@save');

	Route::get('user/{id}', 'LemonTree\Controllers\UserController@getUser')->
		where('id', '[0-9]+');

	Route::post('user/{id}', 'LemonTree\Controllers\UserController@save')->
		where('id', '[0-9]+');

	Route::delete('user/{id}', 'LemonTree\Controllers\UserController@delete')->
		where('id', '[0-9]+');

	Route::get('log', 'LemonTree\Controllers\LogController@getLog');

	Route::get('log/form', 'LemonTree\Controllers\LogController@getForm');

	Route::get('user/list', 'LemonTree\Controllers\UserController@getList');

	Route::get('group/{id}/user/list', 'LemonTree\Controllers\UserController@getListByGroup')->
		where('id', '[0-9]+');

	Route::get('browse/{classId?}', 'LemonTree\Controllers\BrowseController@getIndex');

	Route::get('search/items', 'LemonTree\Controllers\SearchController@getItems');

	Route::get('search/item/{class}', 'LemonTree\Controllers\SearchController@getItem');

	Route::get('search/{class}', 'LemonTree\Controllers\BrowseController@getSearch');

	Route::get('trash/items', 'LemonTree\Controllers\TrashController@getItems');

	Route::get('trash/item/{class}', 'LemonTree\Controllers\TrashController@getItem');

	Route::get('trash/{class}', 'LemonTree\Controllers\BrowseController@getTrash');

	Route::get('binds/{classId?}', 'LemonTree\Controllers\BrowseController@getBinds');

	Route::get('plugin/browse/{classId}', 'LemonTree\Controllers\PluginController@getBrowsePlugin');

	Route::get('favorites', 'LemonTree\Controllers\FavoritesController@getList');

	Route::post('favorites/{classId}', 'LemonTree\Controllers\FavoritesController@postToggle');

	Route::get('tree', 'LemonTree\Controllers\TreeController@show');

	Route::get('hint/{class}', 'LemonTree\Controllers\HintController@getHint');

	Route::get('element/{classId}', 'LemonTree\Controllers\EditController@getElement');

});
