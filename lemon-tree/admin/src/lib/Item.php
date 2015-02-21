<?php namespace LemonTree;

use LemonTree\Properties\OrderProperty;
use LemonTree\Properties\DatetimeProperty;
use LemonTree\Properties\BaseProperty;

class Item {

	public $properties = array();

	protected $name = null;
	protected $title = null;
	protected $mainProperty = null;
	protected $root = false;
	protected $orderProperty = null;
	protected $elementPermissions = false;
	protected $binds = false;
	protected $perPage = null;
	protected $orderBy = array();

	public function __construct($name) {

		static::assertClass($name);

		$this->name = $name;

		return $this;
	}

	public static function create($name)
	{
		return new self($name);
	}

	public static function assertClass($name)
	{
		$implements = class_implements($name);

		if ( ! isset($implements['LemonTree\ElementInterface'])) {
			throw new \Exception(
				"Class $name must implement interface"
				." LemonTree\ElementInterface."
			);
		}

		$traits = class_uses($name);

		if ( ! isset($traits['LemonTree\ElementTrait'])) {
			throw new \Exception(
				"Class $name must use trait"
				." LemonTree\ElementTrait."
			);
		}
	}

	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getNameId()
	{
		return str_replace('\\', Element::ID_SEPARATOR, $this->name);
	}

	public function getClass()
	{
		return new $this->name;
	}

	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setMainProperty($mainProperty)
	{
		$this->mainProperty = $mainProperty;

		return $this;
	}

	public function getMainProperty()
	{
		return $this->mainProperty;
	}

	public function setRoot($root)
	{
		$this->root = $root;

		return $this;
	}

	public function getRoot()
	{
		return $this->root;
	}

	public function setOrderProperty($orderProperty)
	{
		$this->orderProperty = $orderProperty;

		return $this;
	}

	public function getOrderProperty()
	{
		return $this->orderProperty;
	}

	public function setElementPermissions($elementPermissions)
	{
		$this->elementPermissions = $elementPermissions;

		return $this;
	}

	public function getElementPermissions()
	{
		return $this->elementPermissions;
	}

	public function bindItem($name)
	{
		$this->binds[$name] = $name;

		return $this;
	}

	public function getBinds()
	{
		return $this->binds;
	}

	public function setPerPage($perPage)
	{
		$this->perPage = $perPage;

		return $this;
	}

	public function getPerPage()
	{
		return $this->perPage;
	}

	public function addOrderBy($field, $direction = 'asc')
	{
		$this->orderBy[$field] = $direction;

		return $this;
	}

	public function getOrderByList()
	{
		return $this->orderBy;
	}

	public function addProperty(BaseProperty $property)
	{
		$property->setItem($this);

		$this->properties[$property->getName()] = $property;

		return $this;
	}

	public function addOrder($name = 'order', $direction = 'asc')
	{
		$this->
		addOrderBy($name, $direction)->
		addProperty(
			OrderProperty::create($name)
		);

		return $this;
	}

	public function addTimestamps()
	{
		$this->
		addProperty(
			DatetimeProperty::create('created_at')->
			setTitle('Создан')->
			setReadonly(true)->
			setShow(true)
		)->
		addProperty(
			DatetimeProperty::create('updated_at')->
			setTitle('Изменен')->
			setReadonly(true)->
			setShow(true)
		);

		return $this;
	}

	public function addSoftDeletes()
	{
		$this->
		addProperty(
			DatetimeProperty::create('deleted_at')->
			setTitle('Удален')->
			setReadonly(true)->
			setShow(true)
		);

		return $this;
	}

	public function getPropertyList()
	{
		return $this->properties;
	}

	public function getPropertyByName($name)
	{
		return
			isset($this->properties[$name])
			? $this->properties[$name]
			: null;
	}

}
