<?php namespace LemonTree;

use Illuminate\Database\Eloquent\SoftDeletes;

trait ElementTrait {

	use SoftDeletes;

	protected static $map = array();

	protected $item = null;
	protected $assetsName = 'assets';

	public function getDates()
	{
		return array(
			'created_at',
			'updated_at',
			'deleted_at',
		);
	}

	public static function boot()
	{
		parent::boot();

		static::deleting(function($element) {

			if ($element->forceDeleting) return true;

			$childItemList = $element->getChildItemList();

			if (isset($childItemList[OneToOneProperty::RESTRICT])) {
				foreach ($childItemList[OneToOneProperty::RESTRICT] as $itemName => $data) {
					foreach ($data as $propertyName => $property) {
						$count = $element->
							hasMany($itemName, $propertyName)->
							count();
						if ($count > 0) return false;
					}
				}
			} elseif (isset($childItemList[OneToOneProperty::CASCADE])) {
				foreach ($childItemList[OneToOneProperty::CASCADE] as $itemName => $data) {
					foreach ($data as $propertyName => $property) {
						$count = $element->
							hasMany($itemName, $propertyName)->
							count();
						if ($count > 0) {
							$result = $element->
								hasMany($itemName, $propertyName)->
								delete();
							if ( ! $result) return false;
						}
					}
				}
			} elseif (isset($childItemList[OneToOneProperty::SETNULL])) {
				foreach ($childItemList[OneToOneProperty::SETNULL] as $itemName => $data) {
					foreach ($data as $propertyName => $property) {
						if ($property->getRequired()) return false;
						$element->
							hasMany($itemName, $propertyName)->
							update(array($propertyName => null));
					}
				}
			}

			return true;

		});

		static::created(function($element) {

			$class = get_class($element);

			\Cache::tags($class)->flush();

		});

		static::saved(function($element) {

			$class = get_class($element);
			$key = $class::getCacheKey($element->id);

			\Cache::forget($key);
			\Cache::tags($class)->flush();

		});

		static::deleted(function($element) {

			$class = get_class($element);
			$key = $class::getCacheKey($element->id);

			\Cache::forget($key);
			\Cache::tags($class)->flush();

		});
    }

	/*
	public function newQuery($excludeDeleted = true)
	{
		$builder = parent::newQuery();

		return $builder->
			cacheTags(get_called_class())->
			rememberForever();
	}
	 */

	/*
	public static function find($id, $columns = array('*'))
	{
		if (is_array($id) && empty($id)) return new \Collection;

		$instance = new static;

		$class = get_called_class();

		if (isset(static::$map[$class][$id])) {
			return static::$map[$class][$id];
		}

		$key = static::getCacheKey($id);

		$result = $instance->newQuery()->
			rememberForever($key)->
			find($id, $columns);

		static::$map[$class][$id] = $result;

		return $result;
	}
	 */

	public function deleteFromTrash()
	{
		$item = $this->getItem();

		$propertyList = $item->getPropertyList();

		foreach ($propertyList as $propertyName => $property) {
			$property->setElement($this)->drop();
		}

		$this->forceDelete();
	}

	public function copy()
	{
		$item = $this->getItem();

		$propertyList = $item->getPropertyList();

		$clone = new $this;

		foreach ($propertyList as $propertyName => $property) {
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

			if ($property instanceof OrderProperty) {
				$property->setElement($clone)->set();
			} else {
				$clone->$propertyName = $this->$propertyName;
			}
		}

		$clone->save();

		return $clone;
	}

	public static function getCacheKey($id)
	{
		return get_called_class().'.'.$id;
	}

	public static function getByClassId($classId)
	{
		if ( ! strpos($classId, Element::ID_SEPARATOR)) return null;

		try {

			list($class, $id) = explode(Element::ID_SEPARATOR, $classId);

			return $class::find($id);

		} catch (\Exception $e) {}

		return null;
	}

	public static function getWithTrashedByClassId($classId)
	{
		if ( ! strpos($classId, Element::ID_SEPARATOR)) return null;

		try {

			list($class, $id) = explode(Element::ID_SEPARATOR, $classId);

			return
				$class::withTrashed()->
				cacheTags($class)->
				rememberForever()->
				find($id);

		} catch (\Exception $e) {}

		return null;
	}

	public static function getOnlyTrashedByClassId($classId)
	{
		if ( ! strpos($classId, Element::ID_SEPARATOR)) return null;

		try {

			list($class, $id) = explode(Element::ID_SEPARATOR, $classId);

			return
				$class::onlyTrashed()->
				cacheTags($class)->
				rememberForever()->
				find($id);

		} catch (\Exception $e) {}

		return null;
	}

	public function getItem()
	{
		if ($this->item) return $this->item;

		$site = \App::make('site');

		$class = $this->getClass();

		$this->item = $site->getItemByName($class);

		return $this->item;
	}

	public function getClass()
	{
		return get_class($this);
	}

	public function getClassId()
	{
		return Element::getClassId($this);
	}

	public function getProperty($name)
	{
		$item = $this->getItem();

		$property = $item->getPropertyByName($name);

		return $property->setElement($this);
	}

	public function equalTo($element)
	{
		return
			$element instanceof ElementInterface
			&& $this->getClassId() == $element->getClassId()
			? true : false;
	}

	public function getAssetsName()
	{
		return $this->assetsName;
	}

	public function getFolderName()
	{
		return $this->getTable();
	}

	public function getFolderHash()
	{
		return null;
	}

	public function getFolder()
	{
		return
			$this->getAssetsName().DIRECTORY_SEPARATOR
			.$this->getFolderName();
	}

	public function setParent($parent)
	{
		$item = $this->getItem();

		$propertyList = $item->getPropertyList();

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property instanceof OneToOneProperty
				&& $property->getRelatedClass() == $parent->getClass()
			) {
				$this->$propertyName = $parent->id;
			}
		}

		return $this;
	}

	public function getParent()
	{
		$item = $this->getItem();

		$propertyList = $item->getPropertyList();

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property instanceof OneToOneProperty
				&& $property->getRelatedClass()
				&& $property->getParent()
				&& $this->$propertyName
			) {
				return
					$this->belongsTo($property->getRelatedClass(), $propertyName)->
					cacheTags($property->getRelatedClass())->
					rememberForever()->
					first();
			}
		}

		foreach ($propertyList as $propertyName => $property) {
			if (
				$property instanceof OneToOneProperty
				&& $property->getRelatedClass()
				&& $this->$propertyName
			) {
				return
					$this->belongsTo($property->getRelatedClass(), $propertyName)->
					cacheTags($item->getName())->
					rememberForever()->
					first();
			}
		}

		return null;
	}

	public function getParentList()
	{
		$parents = array();
		$parentList = array();
		$exists = array();

		$count = 0;
		$parent = $this->getParent();

		while ($count < 100 && $parent instanceof ElementInterface) {
			if (isset($exists[$parent->getClassId()])) break;
			$parents[] = $parent;
			$exists[$parent->getClassId()] = $parent->getClassId();
			$parent = $parent->getParent();
			$count++;
		}

		krsort($parents);

		foreach ($parents as $parent) {
			$parentList[] = $parent;
		}

		return $parentList;
	}

	public function getChildItemList()
	{
		$childItemList = array();

		$site = \App::make('site');

		$class = $this->getClass();

		$itemList = $site->getItemList();

		foreach ($itemList as $itemName => $item) {
			$propertyList = $item->getPropertyList();
			foreach ($propertyList as $propertyName => $property) {
				if (
					$property instanceof OneToOneProperty
					&& $property->getRelatedClass()
					&& $property->getRelatedClass() == $class
				) {
					$deleting = $property->getDeleting();
					$childItemList[$deleting][$itemName][$propertyName] = $property;
				}
			}
		}

		return $childItemList;
	}

	public function getHref()
	{
		return null;
	}

	public function getBrowseUrl()
	{
		$route = $this->trashed() ? 'admin.trash' : 'admin.browse';

		return \URL::route($route, array($this->getClassId()));
	}

	public function getBrowseUrlAddTab()
	{
		return \URL::route('admin.browse.addtab', array($this->getClassId()));
	}

	public function getEditUrl()
	{
		return \URL::route('admin.edit', array($this->getClassId()));
	}

	public function getEditUrlAddTab()
	{
		return \URL::route('admin.edit.addtab', array($this->getClassId()));
	}

	public function getCopyUrl()
	{
		return \URL::route('admin.copy', array($this->getClassId()));
	}

	public function getDeleteUrl()
	{
		return \URL::route('admin.delete', array($this->getClassId()));
	}

	public function getTrashUrl()
	{
		return \URL::route('admin.trash', array($this->getClassId()));
	}

	public function getRestoreUrl()
	{
		return \URL::route('admin.restore', array($this->getClassId()));
	}

}