<?php namespace LemonTree;

use LemonTree\ElementInterface;
use LemonTree\Properties\FileProperty;
use LemonTree\Properties\ImageProperty;
use LemonTree\Properties\OrderProperty;

final class Element {

	const ID_SEPARATOR = '.';

	public static function getClassId(ElementInterface $element)
	{
		return
			str_replace(
				'\\',
				static::ID_SEPARATOR,
				$element->getClass()
			)
			.static::ID_SEPARATOR
			.$element->id;
	}

	public static function getByClassId($classId)
	{
		if ( ! strpos($classId, static::ID_SEPARATOR)) return null;

		try {

			$array = explode(static::ID_SEPARATOR, $classId);
			$id = array_pop($array);
			$class = implode('\\', $array);

			return \Cache::rememberForever(
				"getByClassId($classId)",
				function () use ($class, $id) {
					return $class::find($id);
				}
			);

		} catch (\Exception $e) {}

		return null;
	}

	public static function getWithTrashedByClassId($classId)
	{
		if ( ! strpos($classId, static::ID_SEPARATOR)) return null;

		try {

			$array = explode(static::ID_SEPARATOR, $classId);
			$id = array_pop($array);
			$class = implode('\\', $array);

			return \Cache::rememberForever(
				"getWithTrashedByClassId($classId)",
				function () use ($class, $id) {
					return $class::withTrashed()->find($id);
				}
			);

		} catch (\Exception $e) {}

		return null;
	}

	public static function getOnlyTrashedByClassId($classId)
	{
		if ( ! strpos($classId, static::ID_SEPARATOR)) return null;

		try {

			$array = explode(static::ID_SEPARATOR, $classId);
			$id = array_pop($array);
			$class = implode('\\', $array);

			return \Cache::rememberForever(
				"getOnlyTrashedByClassId($classId)",
				function () use ($class, $id) {
					return $class::onlyTrashed()->find($id);
				}
			);

		} catch (\Exception $e) {}

		return null;
	}

	public static function getParent(ElementInterface $element)
	{
		$item = $element->getItem();

		$propertyList = $item->getPropertyList();

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property->isOneToOne()
				&& $property->getRelatedClass()
				&& $property->getParent()
				&& $element->$propertyName
			) {
				$classId =
					$property->getRelatedClass()
					.static::ID_SEPARATOR
					.$element->$propertyName;

				return \Cache::rememberForever(
					"getByClassId($classId)",
					function() use ($element, $property, $propertyName) {
						return
							$element->belongsTo(
								$property->getRelatedClass(),
								$propertyName
							)->
							first();
					}
				);
			}
		}

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property->isOneToOne()
				&& $property->getRelatedClass()
				&& $element->$propertyName
			) {
				$classId =
					$property->getRelatedClass()
					.static::ID_SEPARATOR
					.$element->$propertyName;

				return \Cache::rememberForever(
					"getByClassId($classId)",
					function() use ($element, $property, $propertyName) {
						return
							$element->belongsTo(
								$property->getRelatedClass(),
								$propertyName
							)->
							first();
					}
				);
			}
		}

		return null;
	}

	public static function getParentList(ElementInterface $element)
	{
		$parents = [];
		$parentList = [];
		$exists = [];

		$count = 0;
		$parent = self::getParent($element);

		while ($count < 100 && $parent instanceof ElementInterface) {
			if (isset($exists[$parent->getClassId()])) break;
			$parents[] = $parent;
			$exists[$parent->getClassId()] = $parent->getClassId();
			$parent = self::getParent($parent);
			$count++;
		}

		krsort($parents);

		foreach ($parents as $parent) {
			$parentList[] = $parent;
		}

		return $parentList;
	}

	public static function copy(ElementInterface $element)
	{
		$item = $element->getItem();

		$propertyList = $item->getPropertyList();

		$clone = new $element;

		foreach ($propertyList as $propertyName => $property) {
			if ($property instanceof OrderProperty) {
				$property->setElement($clone)->set();
				continue;
			}

			if (
				$property->getHidden()
				|| $property->getReadonly()
			) continue;

			if (
				(
					$property instanceof FileProperty
					|| $property instanceof ImageProperty
				)
				&& ! $property->getRequired()
			) continue;

			$clone->$propertyName = $element->$propertyName;
		}

		$clone->save();

		\Cache::tags($element->getClass())->flush();

		return $clone;
	}

	public static function delete(ElementInterface $element)
	{
		$site = \App::make('site');

		$class = $element->getClass();

		$itemList = $site->getItemList();

		foreach ($itemList as $item) {
			$itemName = $item->getName();
			$propertyList = $item->getPropertyList();

			foreach ($propertyList as $property) {
				if (
					$property->isOneToOne()
					&& $property->getRelatedClass() == $class
				) {
					$count = $element->
						hasMany($itemName, $property->getName())->
						count();

					if ($count) return false;
				}
			}
		}

		$element->delete();

		\Cache::tags($element->getClass())->flush();

		\Cache::forget("getByClassId({$element->getClassId()})");

		\Cache::forget("getWithTrashedByClassId({$element->getClassId()})");

		\Cache::forget("getOnlyTrashedByClassId({$element->getClassId()})");

		return true;
	}

	public static function deleteFromTrash(ElementInterface $element)
	{
		$item = $element->getItem();

		$propertyList = $item->getPropertyList();

		foreach ($propertyList as $propertyName => $property) {
			$property->setElement($element)->drop();
		}

		$element->forceDelete();

		\Cache::tags($element->getClass())->flush();

		\Cache::forget("getByClassId({$element->getClassId()})");

		\Cache::forget("getWithTrashedByClassId({$element->getClassId()})");

		\Cache::forget("getOnlyTrashedByClassId({$element->getClassId()})");

		return true;
	}

	public static function restore(ElementInterface $element)
	{
		$element->restore();

		\Cache::tags($element->getClass())->flush();

		\Cache::forget("getByClassId({$element->getClassId()})");

		\Cache::forget("getWithTrashedByClassId({$element->getClassId()})");

		\Cache::forget("getOnlyTrashedByClassId({$element->getClassId()})");

		return true;
	}

	public static function save(ElementInterface $element)
	{
		$element->save();

		\Cache::tags($element->getClass())->flush();

		\Cache::forget("getByClassId({$element->getClassId()})");

		\Cache::forget("getWithTrashedByClassId({$element->getClassId()})");

		\Cache::forget("getOnlyTrashedByClassId({$element->getClassId()})");

		return true;
	}

}