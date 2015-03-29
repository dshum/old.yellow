<?php namespace LemonTree\Controllers;

use LemonTree\Item;
use LemonTree\Element;
use LemonTree\LoggedUser;
use LemonTree\Models\UserAction;
use LemonTree\UserActionType;

class OrderController extends Controller {

	public function save($class)
	{
		$loggedUser = LoggedUser::getUser();

		$site = \App::make('site');

		$currentItem = $site->getItemByName($class);

		$orderList = \Input::get('orders');

		if (
			! $currentItem instanceof Item
			|| ! $orderList
		) {
			$scope['state'] = 'error_wrong_parameters';
			return json_encode($scope);
		}

		$orderProperty = $currentItem->getOrderProperty();

		if ( ! $orderProperty) {
			$scope['state'] = 'error_no_order_property';
			return json_encode($scope);
		}

		$saved = array();

		foreach ($orderList as $classId => $order) {
			$element = Element::getByClassId($classId);

			if ($element) {
				$element->$orderProperty = $order;

				$element->save();

				\Cache::tags($element->getClass())->flush();

				\Cache::forget("getByClassId({$element->getClassId()})");

				\Cache::forget("getWithTrashedByClassId({$element->getClassId()})");

				\Cache::forget("getOnlyTrashedByClassId({$element->getClassId()})");

				$saved[] = $element->getClassId();
			}
		}

		if (sizeof($saved)) {
			UserAction::log(
				UserActionType::ACTION_TYPE_ORDER_ELEMENT_LIST_ID,
				implode(', ', $saved)
			);
		}

		$scope['state'] = 'ok';

		return \Response::json($scope);
	}

	public function index($class, $classId = null)
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		$site = \App::make('site');

		if ($classId) {
			$currentElement = Element::getByClassId($classId);

			if ( ! $currentElement) {
				$scope['state'] = 'error_element_not_found';
				return \Response::json($scope);
			}

			if ( ! $loggedUser->hasViewAccess($currentElement)) {
				$scope['state'] = 'error_element_access_denied';
				return \Response::json($scope);
			}

			$currentElementItem = $site->getItemByName($currentElement->getClass());
			$mainProperty = $currentElementItem->getMainProperty();

			$currentElement->classId = $currentElement->getClassId();
			$currentElement->mainProperty = $currentElement->$mainProperty;
			$currentElement->trashed = $currentElement->trashed();

			$parentElement = Element::getParent($currentElement);

			if ($parentElement) {
				$parentElement->classId = $parentElement->getClassId();
			}

			$parentList = Element::getParentList($currentElement);

			foreach ($parentList as $parent) {
				$parent->classId = $parent->getClassId();
				$parent->mainProperty = $parent->$mainProperty;
			}
		} else {
			$currentElement = null;
			$parentElement = null;
			$parentList = [];
		}

		$currentItem = $site->getItemByName($class);

		if ( ! $currentItem) {
			$scope['currentElement'] = $currentElement;
			$scope['state'] = 'error_item_not_found';
			return \Response::json($scope);
		}

		if ( ! $currentItem->getOrderProperty()) {
			$scope['currentElement'] = $currentElement;
			$scope['state'] = 'error_no_order_property';
			return json_encode($scope);
		}

		$item = [
			'name' => $currentItem->getName(),
			'nameId' => $currentItem->getNameId(),
			'title' => $currentItem->getTitle(),
		];

		$elementList = $this->getElementList($currentItem, $currentElement);

		$scope['currentElement'] = $currentElement;
		$scope['parentElement'] = $parentElement;
		$scope['parentList'] = $parentList;
		$scope['currentItem'] = $item;
		$scope['elementList'] = $elementList;

		return \Response::json($scope);
	}

	protected function getElementList(
		Item $item,
		$currentElement = null
	)
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		$propertyList = $item->getPropertyList();

		if ( ! $currentElement && ! $item->getRoot()) {
			return $scope;
		}

		if ( ! $loggedUser->isSuperUser()) {
			$permissionDenied = true;
			$deniedElementList = array();
			$allowedElementList = array();

			$groupList = $loggedUser->getGroups();

			foreach ($groupList as $group) {
				$itemPermission = $group->getItemPermission($item->getName())
					? $group->getItemPermission($item->getName())->permission
					: $group->default_permission;

				if ($itemPermission != 'deny') {
					$permissionDenied = false;
					$deniedElementList = array();
				}

				$elementPermissionList = $group->elementPermissions;

				$elementPermissionMap = array();

				foreach ($elementPermissionList as $elementPermission) {
					$classId = $elementPermission->class_id;
					$permission = $elementPermission->permission;
					list($class, $id) = explode(Element::ID_SEPARATOR, $classId);
					if ($class == $item->getName()) {
						$elementPermissionMap[$id] = $permission;
					}
				}

				foreach ($elementPermissionMap as $id => $permission) {
					if ($permission == 'deny') {
						$deniedElementList[$id] = $id;
					} else {
						$allowedElementList[$id] = $id;
					}
				}
			}
		}

		$elementListCriteria = $item->getClass()->where(
			function($query) use ($propertyList, $currentElement) {
				if ($currentElement) {
					$query->orWhere('id', null);
				}

				foreach ($propertyList as $propertyName => $property) {
					if (
						$currentElement
						&& $property->isOneToOne()
						&& $property->getRelatedClass() == $currentElement->getClass()
					) {
						$query->orWhere(
							$property->getName(), $currentElement->id
						);
					} elseif (
						! $currentElement
						&& $property->isOneToOne()
					) {
						$query->orWhere(
							$property->getName(), null
						);
					}
				}
			}
		);

		if ( ! $loggedUser->isSuperUser()) {
			if (
				$permissionDenied
				&& sizeof($allowedElementList)
			) {
				$elementListCriteria->whereIn('id', $allowedElementList);
			} elseif (
				! $permissionDenied
				&& sizeof($deniedElementList)
			) {
				$elementListCriteria->whereNotIn('id', $deniedElementList);
			} elseif ($permissionDenied) {
				return $scope;
			}
		}

		$total = $elementListCriteria->count();

		if ( ! $total) {
			return $scope;
		}

		$orderByList = $item->getOrderByList();

		foreach ($orderByList as $field => $direction) {
			$elementListCriteria->orderBy($field, $direction);
		}

		$elementList = $elementListCriteria->get();

		$elements = [];

		foreach ($elementList as $element) {
			$elements[] = [
				'id' => $element->id,
				'class' => $element->getClass(),
				'classId' => $element->getClassId(),
				'mainProperty' => $element->{$item->getMainProperty()},
			];
		}

		return $elements;
	}

}