<?php namespace LemonTree\Controllers;

use LemonTree\LoggedUser;

class HintController extends Controller {

	const HINT_LIMIT = 20;

	public function hint($class)
	{
		$scope = array();

		try {

			$site = \App::make('site');

			$item = $site->getItemByName($class);
			$mainProperty = $item->getMainProperty();

			$term = \Input::get('term');

			$elementListCriteria = $item->getClass()->query();

			if ($term) {
				$elementListCriteria->
				whereRaw(
					"cast(id as text) ilike :term or $mainProperty ilike :term",
					array('term' => '%'.$term.'%')
				);
			}

			$orderByList = $item->getOrderByList();

			foreach ($orderByList as $field => $direction) {
				$elementListCriteria->orderBy($field, $direction);
			}

			$elementListCriteria->limit(static::HINT_LIMIT);

			$elementList = $elementListCriteria->get();

			$prev = null;
			$k = 2;

			foreach ($elementList as $element) {
				$id = $element->id;
				$classId = $element->getClassId();
				$name = $element->$mainProperty;
				if ($prev == $name) {
					$name = $name.' '.$k;
					$k++;
				} else {
					$name = $name;
					$k = 2;
				}
				$scope[] = array(
					'id' => $id,
					'classId' => $classId,
					'value' => $name,
				);
				$prev = $element->$mainProperty;
			}

		} catch (\Exception $e) {
			ErrorMessageUtils::sendMessage($e);
		}

		return \Response::json($scope);
	}

	public function multiHint($itemName, $propertyName)
	{
		$scope = array();

		try {

			$site = \App::make('site');

			$item = $site->getItemByName($itemName);
			$property = $item ? $item->getPropertyByName($propertyName) : null;
			$items = $property ? $property->getItems() : null;

			if ( ! $items) return $scope;

			$term = \Input::get('term');

			$prev = null;
			$k = 2;

			foreach ($items as $itemName) {

				$item = $site->getItemByName($itemName);

				if ( ! $item) continue;

				$mainProperty = $item->getMainProperty();

				$elementListCriteria = $itemName::query();

				if ($term) {
					$elementListCriteria->
					where(
						'id', 'ilike', '%'.$term.'%'
					)->
					orWhere(
						$mainProperty, 'ilike', '%'.$term.'%'
					);
				}

				$orderByList = $item->getOrderByList();

				foreach ($orderByList as $field => $direction) {
					$elementListCriteria->orderBy($field, $direction);
				}

				$elementListCriteria->
				limit(static::HINT_LIMIT);

				$elementListCriteria->
				cacheTags($itemName)->
				rememberForever();

				$elementList = $elementListCriteria->get();

				foreach ($elementList as $element) {
					$id = $element->getClassId();
					$name = $element->$mainProperty;
					if ($prev == $name) {
						$name = $name.' '.$k;
						$k++;
					} else {
						$name = $name;
						$k = 2;
					}
					$scope[] = array(
						'id' => $id,
						'value' => $name,
					);
					$prev = $element->$mainProperty;
				}

			}

			$scope = array_slice($scope, 0, static::HINT_LIMIT);

		} catch (\Exception $e) {
			ErrorMessageUtils::sendMessage($e);
		}

		return \Response::json($scope);
	}

}
