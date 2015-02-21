<?php namespace LemonTree\Controllers;

use LemonTree\Site;
use LemonTree\Element;
use LemonTree\LoggedUser;

class TreeController extends Controller {

	public function show($classId = null)
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		$tree = [];

		$parent = $classId
			? Element::getByClassId($classId)
			: null;

		$site = \App::make('site');

		$itemList = $site->getItemList();
		$binds = $site->getBindsTree();

		$bindItemList = array();

		foreach ($itemList as $itemName => $item) {
			if ($parent) {
				if (isset($binds[$parent->getClass()][$itemName])) {
					$bindItemList[$itemName] = $item;
				}
				if (isset($binds[$parent->getClassId()][$itemName])) {
					$bindItemList[$itemName] = $item;
				}
			} else {
				if (isset($binds[Site::ROOT][$itemName])) {
					$bindItemList[$itemName] = $item;
				}
			}
		}

		if ( ! $bindItemList && $parent) return null;

		$items = array();
		$itemElementList = array();
		$subTree = array();
		$treeCount = array();

		foreach ($bindItemList as $itemName => $item) {

			$propertyList = $item->getPropertyList();

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

			$elementListCriteria = $item->getClass()->where(
				function($query) use ($propertyList, $parent) {
					if ($parent) {
						foreach ($propertyList as $propertyName => $property) {
							if (
								$property->isOneToOne()
								&& $property->getRelatedClass() == $parent->getClass()
							) {
								$query->orWhere(
									$property->getName(), $parent->id
								);
							}
						}
					} else {
						foreach ($propertyList as $propertyName => $property) {
							if ($property->isOneToOne()) {
								$query->orWhere(
									$property->getName(), null
								);
							}
						}
					}
				}
			);

			if ($loggedUser->isSuperUser()) {

			} elseif (
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
				unset($bindItemList[$itemName]);
				continue;
			}

			$orderByList = $item->getOrderByList();

			foreach ($orderByList as $field => $direction) {
				$elementListCriteria->orderBy($field, $direction);
			}

			$elementList = $elementListCriteria->get();

			if (sizeof ($elementList)) {
				$items[] = [
					'name' => $itemName,
					'title' => $item->getTitle(),
				];

				foreach ($elementList as $element) {
					$itemElementList[$itemName][] = [
						'classId' => $element->getClassId(),
						'mainProperty' => $element->{$item->getMainProperty()},
					];
				}

				foreach ($elementList as $element) {
					$json = $this->show($element->getClassId());
					$array = $json ? json_decode($json->getContent()) : null;
					$subTree[$element->getClassId()] = $array;
				}
			} else {
				unset($bindItemList[$itemName]);
			}

		}

		if ( ! $itemElementList && $parent) return null;

		$scope['parent'] = $parent;
		$scope['itemList'] = $items;
		$scope['itemElementList'] = $itemElementList;
		$scope['subTree'] = $subTree;

		return \Response::json($scope);
	}

}
