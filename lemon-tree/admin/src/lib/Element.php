<?php namespace LemonTree;

final class Element {

	const ID_SEPARATOR = '.';

	public static function getClassId(ElementInterface $element)
	{
		return
			str_replace('\\', Element::ID_SEPARATOR, $element->getClass())
			.Element::ID_SEPARATOR
			.$element->id;
	}

	public static function getByClassId($classId)
	{
		if ( ! strpos($classId, self::ID_SEPARATOR)) return null;

		try {

			$array = explode(static::ID_SEPARATOR, $classId);
			$id = array_pop($array);
			$class = implode('\\', $array);

			return $class::find($id);

		} catch (\Exception $e) {}

		return null;
	}

	public static function getWithTrashedByClassId($classId)
	{
		if ( ! strpos($classId, self::ID_SEPARATOR)) return null;

		try {

			$array = explode(static::ID_SEPARATOR, $classId);
			$id = array_pop($array);
			$class = implode('\\', $array);

			return $class::withTrashed()->find($id);

		} catch (\Exception $e) {}

		return null;
	}

	public static function getOnlyTrashedByClassId($classId)
	{
		if ( ! strpos($classId, self::ID_SEPARATOR)) return null;

		try {

			$array = explode(static::ID_SEPARATOR, $classId);
			$id = array_pop($array);
			$class = implode('\\', $array);

			return $class::onlyTrashed()->find($id);

		} catch (\Exception $e) {}

		return null;
	}

}