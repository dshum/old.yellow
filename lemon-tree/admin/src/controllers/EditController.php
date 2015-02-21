<?php namespace LemonTree\Controllers;

use LemonTree\Site;
use LemonTree\Item;
use LemonTree\Element;
use LemonTree\LoggedUser;

class EditController extends Controller {

	public function getElement($classId)
	{
		$scope = array();

		$currentElement = Element::getWithTrashedByClassId($classId);

		if ( ! $currentElement) {
			$scope['state'] = 'error_element_not_found';
			return \Response::json($scope);
		}

		$loggedUser = LoggedUser::getUser();

		if ( ! $loggedUser->hasViewAccess($currentElement)) {
			$scope['state'] = 'error_element_access_denied';
			return \Response::json($scope);
		}

		$site = \App::make('site');

		$currentItem = $site->getItemByName($currentElement->getClass());
		$mainProperty = $currentItem->getMainProperty();

		$item = [
			'name' => $currentItem->getName(),
			'title' => $currentItem->getTitle(),
		];

		$currentElement->classId = $currentElement->getClassId();
		$currentElement->mainProperty = $currentElement->$mainProperty;
		$currentElement->trashed = $currentElement->trashed();

		$parentElement = $currentElement->getParent();

		if ($parentElement) {
			$parentElement->classId = $parentElement->getClassId();
		}

		$parentList = $currentElement->getParentList();

		foreach ($parentList as $parent) {
			$parent->classId = $parent->getClassId();
			$parent->mainProperty = $parent->$mainProperty;
		}

		$propertyList = $currentItem->getPropertyList();

		$properties = [];

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property->getHidden()
			) continue;

			if (
				! $currentElement->trashed()
				&& $propertyName == 'deleted_at'
			) continue;

			$property->setElement($currentElement);

			$properties[] = [
				'name' => $property->getName(),
				'title' => $property->getTitle(),
				'class' => $property->getClassName(),
				'readonly' => $property->getReadonly(),
				'isMainProperty' => $property->isMainProperty(),
				'element' => $property->getElement(),
				'item' => [
					'name' => $currentItem->getName(),
					'title' => $currentItem->getTitle(),
				],
				'editView' => $property->getEditView(),
			];
		}

		$scope['currentElement'] = $currentElement;
		$scope['parentElement'] = $parentElement;
		$scope['parentList'] = $parentList;
		$scope['currentItem'] = $item;
		$scope['propertyList'] = $properties;

		return \Response::json($scope);
	}

}
