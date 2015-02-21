<?php namespace LemonTree\Controllers;

use Carbon\Carbon;
use LemonTree\Site;
use LemonTree\Item;
use LemonTree\Element;
use LemonTree\LoggedUser;

class TrashController extends Controller {

	public function getItems()
	{
		$scope = array();

		$loggedUser = LoggedUser::getUser();

		$site = \App::make('site');

		$sort = \Input::get('sort');

		$trash = $loggedUser->getParameter('trash') ?: [];

		$currentItem = isset($trash['currentItem'])
			? $site->getItemByName($trash['currentItem'])
			: null;

		if (in_array($sort, array('rate', 'date', 'name', 'default'))) {
			$trash['sortItem'] = $sort;
			$loggedUser->setParameter('trash', $trash);
		}

		$sortItem =
			isset($trash['sortItem'])
			? $trash['sortItem']
			: 'default';

		$itemList = $site->getItemList();

		$map = array();

		if ($sortItem == 'name') {

			foreach ($itemList as $item) {
				$map[$item->getTitle()] = $item;
			}

			ksort($map);

		} elseif ($sortItem == 'date') {

			$sortItemDate = isset($trash['sortItemDate'])
				? $trash['sortItemDate'] : array();

			arsort($sortItemDate);

			foreach ($sortItemDate as $class => $date) {
				$map[$class] = $site->getItemByName($class);
			}

			foreach ($itemList as $item) {
				$map[$item->getNameId()] = $item;
			}

		} elseif ($sortItem == 'rate') {

			$sortItemRate = isset($trash['sortItemRate'])
				? $trash['sortItemRate'] : array();

			arsort($sortItemRate);

			foreach ($sortItemRate as $class => $rate) {
				$map[$class] = $site->getItemByName($class);
			}

			foreach ($itemList as $item) {
				$map[$item->getNameId()] = $item;
			}

		} else {

			foreach ($itemList as $item) {
				$map[] = $item;
			}

		}

		$items = array();

		foreach ($map as $item) {
			$itemCount = $this->getItemCount(
				$item
			);

			if ( ! $itemCount) continue;

			$items[] = [
				'name' => $item->getName(),
				'nameId' => $item->getNameId(),
				'title' => $item->getTitle(),
				'total' => $itemCount,
			];
		}

		unset($map);

		$scope['sortItem'] = $sortItem;
		$scope['itemList'] = $items;
		$scope['currentItem'] = $currentItem
			? [
				'name' => $currentItem->getName(),
				'nameId' => $currentItem->getNameId(),
				'title' => $currentItem->gettitle(),
			] : null;

		return \Response::json($scope);
	}

	protected function getItemCount(Item $item)
	{
		$loggedUser = LoggedUser::getUser();

		$propertyList = $item->getPropertyList();

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

		$elementListCriteria = $item->getClass()->onlyTrashed();

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

		return $total;
	}

}
