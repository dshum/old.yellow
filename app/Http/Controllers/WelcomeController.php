<?php namespace App\Http\Controllers;

class WelcomeController extends Controller {

	public function __construct()
	{
		$this->middleware('guest');
	}

	public function special()
	{
		$scope = array();

		view()->share('currentElement', null);

		$scope = CommonFilter::apply($scope);

		$goodList =
			Good::where('special', true)->
			orderBy('name')->
			get();

		$scope['goodList'] = $goodList;

		return view('catalogue.special', $scope);
	}

	public function novelty()
	{
		$scope = array();

		view()->share('currentElement', null);

		$scope = CommonFilter::apply($scope);

		$goodList =
			Good::where('novelty', true)->
			orderBy('name')->
			get();

		$scope['goodList'] = $goodList;

		return view('catalogue.novelty', $scope);
	}

	public function index()
	{
		$scope = array();

		view()->share('currentElement', null);

		$scope = CommonFilter::apply($scope);

		return view('welcome', $scope);
	}

}
