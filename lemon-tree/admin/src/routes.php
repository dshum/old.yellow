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

	$user = \Cache::tags('User')->rememberForever(
		"getUserById($userId)",
		function() use ($userId) {
			return User::find($userId);
		}
	);

	if ( ! $user) {
		$scope['message'] = "Пользователь с идентификатором $userId не найден.";
		return Response::json($scope, 401);
	}

	LoggedUser::setUser($user);

});

Route::group(array('prefix' => 'admin'), function() {

	Route::get('/', 'LemonTree\Controllers\HomeController@index');

});

Route::group(array('prefix' => 'api'), function() {

	Route::post('login', 'LemonTree\Controllers\LoginController@login');

});

Route::group(array(
	'prefix' => 'api',
	'before' => 'admin.auth'
), function() {

	Route::get('user', 'LemonTree\Controllers\LoginController@user');

	Route::post('profile', 'LemonTree\Controllers\ProfileController@save');

	Route::post('group/add', 'LemonTree\Controllers\GroupController@save');

	Route::get('group/{id}', 'LemonTree\Controllers\GroupController@group')->
		where('id', '[0-9]+');

	Route::post('group/{id}', 'LemonTree\Controllers\GroupController@save')->
		where('id', '[0-9]+');

	Route::delete('group/{id}', 'LemonTree\Controllers\GroupController@delete')->
		where('id', '[0-9]+');

	Route::get('group/{id}/items', 'LemonTree\Controllers\GroupController@itemPermissions')->
		where('id', '[0-9]+');

	Route::post('group/{id}/items', 'LemonTree\Controllers\GroupController@saveItemPermissions')->
		where('id', '[0-9]+');

	Route::get('group/{id}/elements', 'LemonTree\Controllers\GroupController@elementPermissions')->
		where('id', '[0-9]+');

	Route::post('group/{id}/elements', 'LemonTree\Controllers\GroupController@saveElementPermissions')->
		where('id', '[0-9]+');

	Route::get('group/list', 'LemonTree\Controllers\GroupController@groups');

	Route::get('user/form', 'LemonTree\Controllers\UserController@form');

	Route::post('user/add', 'LemonTree\Controllers\UserController@save');

	Route::get('user/{id}', 'LemonTree\Controllers\UserController@user')->
		where('id', '[0-9]+');

	Route::post('user/{id}', 'LemonTree\Controllers\UserController@save')->
		where('id', '[0-9]+');

	Route::delete('user/{id}', 'LemonTree\Controllers\UserController@delete')->
		where('id', '[0-9]+');

	Route::get('log', 'LemonTree\Controllers\LogController@log');

	Route::get('log/form', 'LemonTree\Controllers\LogController@form');

	Route::get('user/list', 'LemonTree\Controllers\UserController@users');

	Route::get('group/{id}/user/list', 'LemonTree\Controllers\UserController@groupUsers')->
		where('id', '[0-9]+');

	Route::get('browse/{classId?}', 'LemonTree\Controllers\BrowseController@index');

	Route::get('list/{class}/{classId?}', 'LemonTree\Controllers\BrowseController@elementList');

	Route::get('search/items', 'LemonTree\Controllers\SearchController@items');

	Route::get('search/item/{class}', 'LemonTree\Controllers\SearchController@item');

	Route::get('search/{class}', 'LemonTree\Controllers\BrowseController@search');

	Route::get('trash/items', 'LemonTree\Controllers\TrashController@items');

	Route::get('trash/item/{class}', 'LemonTree\Controllers\TrashController@item');

	Route::get('trash/{class}', 'LemonTree\Controllers\BrowseController@trash');

	Route::get('binds/{classId?}', 'LemonTree\Controllers\BrowseController@binds');

	Route::get('plugin/browse/{classId}', 'LemonTree\Controllers\PluginController@browsePlugin');

	Route::get('favorites', 'LemonTree\Controllers\FavoritesController@favorites');

	Route::post('favorites/{classId}', 'LemonTree\Controllers\FavoritesController@toggle');

	Route::get('tree', 'LemonTree\Controllers\TreeController@show');

	Route::get('hint/{class}', 'LemonTree\Controllers\HintController@hint');

	Route::get('element/{classId}', 'LemonTree\Controllers\EditController@edit');

	Route::post('element/{classId}', 'LemonTree\Controllers\EditController@save');

	Route::post('copy/{classId}', 'LemonTree\Controllers\EditController@copy');

	Route::post('move/{classId}', 'LemonTree\Controllers\EditController@move');

	Route::post('delete/{classId}', 'LemonTree\Controllers\EditController@delete');

	Route::post('restore/{classId}', 'LemonTree\Controllers\EditController@restore');

	Route::get('order/{class}/{classId?}', 'LemonTree\Controllers\OrderController@index');

	Route::post('order/{class}', 'LemonTree\Controllers\OrderController@save');

});
