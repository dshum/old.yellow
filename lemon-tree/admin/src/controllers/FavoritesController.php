<?php namespace LemonTree\Controllers;

use LemonTree\LoggedUser;
use LemonTree\Models\Favorite;

class FavoritesController extends Controller {

	public function toggle($classId)
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		$favorite =
			Favorite::where(
				function($query) use ($loggedUser, $classId) {
					$query->where('user_id', $loggedUser->id);
					$query->where('class_id', $classId);
				}
			)->
			first();

		if ($favorite) {
			$favorite->delete();
			$scope['result'] = 'remove';
		} else {
			$favorite = new Favorite;
			$favorite->class_id = $classId;
			$favorite->user_id = $loggedUser->id;
			$favorite->save();
			$scope['result'] = 'add';
		}

		$element = $favorite->getElement();
		$item = $element->getItem();
		$mainProperty = $item->getMainProperty();

		$favorite = [
			'id' => $favorite->id,
			'classId' => $element->getClassId(),
			'name' => $element->{$mainProperty},
		];

		$scope['favorite'] = $favorite;

		return \Response::json($scope);
	}

	public function favorites()
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		$favoriteList = \Cache::tags('Favorite')->rememberForever(
			"getFavoriteListByUser({$loggedUser->id}).orderBy(created_at.asc)",
			function () use ($loggedUser) {
				return
					Favorite::where('user_id', $loggedUser->id)->
					orderBy('created_at')->get();
			}
		);

		$favorites = [];

		foreach ($favoriteList as $k => $favorite) {
			$element = $favorite->getElement();
			if ( ! $element) {
				unset($favoriteList[$k]);
				continue;
			}
			$item = $element->getItem();
			$mainProperty = $item->getMainProperty();
			$favorites[] = [
				'id' => $favorite->id,
				'classId' => $element->getClassId(),
				'name' => $element->{$mainProperty},
			];
		}

		$scope['favoriteList'] = $favorites;

		return \Response::json($scope);
	}

}
