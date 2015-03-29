<?php namespace App\Http\Controllers;

use App\Category;

class CommonFilter {

	public static function apply($scope = array()) {

		view()->share('currentElement', null);
		view()->share('currentRouteName', \Route::getCurrentRoute()->getActionName());
		view()->share('loggedUser', null);

		$categoryList = \Cache::tags('App\Category')->rememberForever(
			'CommonFilter::categoryList', function() {
				return Category::orderBy('order')->get();
			}
		);

		$scope['categoryList'] = $categoryList;

		return $scope;
	}

}
