<?php namespace App\Http\Controllers;

use App\Good;

class MoneyStatController extends Controller {

	public function getList()
	{
		$scope = array();

		$name = \Input::get('name');
		$priceFrom = \Input::get('priceFrom');
		$priceTo = \Input::get('priceTo');

		if ($priceTo && $priceFrom > $priceTo) {
			$tmp = $priceFrom;
			$priceFrom = $priceTo;
			$priceTo = $tmp;
		}

		$goodList = Good::where(
			function($query) use ($name, $priceFrom, $priceTo) {
				if ($name) {
					$query->whereRaw(
						"name ilike :term",
						array('term' => '%'.$name.'%')
					);
				}

				if ($priceFrom) {
					$query->where('price', '>=', $priceFrom);
				}

				if ($priceTo) {
					$query->where('price', '<=', $priceTo);
				}
			}
		)->
		orderBy('name')->get();

		$scope['goodList'] = $goodList;

		return response()->json($scope);
	}

	public function getIndex()
	{
		$scope = array();

		return view('plugins.moneyStat', $scope);
	}

}