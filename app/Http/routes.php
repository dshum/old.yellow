<?php

use App\Category;
use App\Subcategory;
use App\Good;
use App\Section;
use App\Http\Controllers\CommonFilter;

Route::get('plugins/moneyStat', 'MoneyStatController@getIndex');

Route::group(['before' => 'admin.auth'], function() {
	Route::get('plugins/moneyStat/list', 'MoneyStatController@getList');
});

Route::group(array('before' => 'guest'), function() {
	Route::get('/register', array('as' => 'register', 'uses' => 'RegisterController@getIndex'));
	Route::get('/login', array('as' => 'login', 'uses' => 'LoginController@getIndex'));
	Route::post('/login', array('as' => 'login', 'uses' => 'LoginController@postLogin'));
	Route::get('/restore', array('as' => 'restore', 'uses' => 'RestoreController@getIndex'));
});

Route::group(array('before' => 'auth'), function() {
	Route::get('/logout', array('as' => 'logout', 'uses' => 'LoginController@getLogout'));
	Route::get('/cabinet', array('as' => 'cabinet', 'uses' => 'CabinetController@getIndex'));
});

Route::get('/', array('as' => 'welcome', 'uses' => 'WelcomeController@index'));

Route::get('/novelty', array('as' => 'novelty', 'uses' => 'HomeController@getNovelty'));

Route::get('/special', array('as' => 'special', 'uses' => 'HomeController@getSpecial'));

Route::get('/cart', array('as' => 'cart', 'uses' => 'CartController@getIndex'));

Route::get('/order', array('as' => 'order', 'uses' => 'OrderController@getIndex'));

Route::get('/delivery', array('as' => 'delivery', function() {
	$currentElement = Section::find(1);
	$scope = CommonFilter::apply();
	view()->share('currentElement', $currentElement);
	return view('common', $scope);
}));

Route::get('/payments', array('as' => 'payments', function() {
	$currentElement = Section::find(2);
	$scope = CommonFilter::apply();
	view()->share('currentElement', $currentElement);
	return view('common', $scope);
}));

Route::get('/contacts', array('as' => 'contacts', function() {
	$currentElement = Section::find(3);
	$scope = CommonFilter::apply();
	view()->share('currentElement', $currentElement);
	return view('common', $scope);
}));

Route::get('/{url1}/{url2?}', array('as' => 'catalogue', function($url1, $url2 = null) {

	$category = Category::where('url', $url1)->first();

	if ( ! $category) App::abort(404);

	if ( ! $url2) {
		return App::make('CategoryController')->index($category);
	}

	$subcategory = Subcategory::where('url', $url2)->first();

	if ($subcategory) {
		return App::make('SubcategoryController')->index($category, $subcategory);
	}

	$good = Good::where('url', $url2)->first();

	if ( ! $good) App::abort(404);

	return App::make('GoodController')->index($category, $good);

}));

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
