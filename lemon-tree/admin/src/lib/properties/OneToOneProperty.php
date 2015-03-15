<?php namespace LemonTree\Properties;

use LemonTree\Item;
use LemonTree\ElementInterface;

class OneToOneProperty extends BaseProperty {

	protected $relatedClass = null;
	protected $parent = false;
	protected $binds = array();

	public function __construct($name) {
		parent::__construct($name);

		$this->
		addRule('integer', 'Идентификатор элемента должен быть целым числом');

		return $this;
	}

	public static function create($name)
	{
		return new self($name);
	}

	public function getRefresh()
	{
		return true;
	}

	public function setRelatedClass($relatedClass)
	{
		Item::assertClass($relatedClass);

		$this->relatedClass = $relatedClass;

		return $this;
	}

	public function getRelatedClass()
	{
		return $this->relatedClass;
	}

	public function setParent($parent)
	{
		$this->parent = $parent;

		return $this;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function bind()
	{
		$num = func_num_args();
		$args = func_get_args();

		if ($num < 1) return $this;

		$name = array_shift($args);

		if ( ! $args) $this->binds[null][$name] = $name;

		foreach ($args as $arg) {
			$this->binds[$name][$arg] = $arg;
		}

		return $this;
	}

	public function getBinds()
	{
		return $this->binds;
	}

	public function setElement(ElementInterface $element)
	{
		$this->element = $element;

		$site = \App::make('site');

		$relatedClass = $this->getRelatedClass();
		$relatedItem = $site->getItemByName($relatedClass);
		$mainProperty = $relatedItem->getMainProperty();
		$id = $this->element->{$this->getName()};

		if ($relatedClass && $id) {
			$this->value = \Cache::rememberForever(
				"$relatedClass.getById($id)",
				function() use ($relatedClass, $id) {
					return $relatedClass::find($id);
				}
			);
		}

		if ($this->value) {
			$this->value->classId = $this->value->getClassId();
			$this->value->value = $this->value->{$mainProperty};
		}

		return $this;
	}

	public function searchQuery($query)
	{
		$name = $this->getName();

		$value = (int)\Input::get($name);

		if ($value) {
			$query->where($name, $value);
		}

		return $query;
	}

	public function searching()
	{
		$name = $this->getName();

		$value = (int)\Input::get($name);

		return $value
			? true : false;
	}

	public function getEditView()
	{
		$site = \App::make('site');

		$relatedClass = $this->getRelatedClass();
		$relatedItem = $site->getItemByName($relatedClass);

		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $this->getValue(),
			'readonly' => $this->getReadonly(),
			'required' => $this->getRequired(),
			'relatedClass' => $relatedItem->getNameId(),
		);

		return $scope;
	}

	public function getMoveView()
	{
		$site = \App::make('site');

		$relatedClass = $this->getRelatedClass();
		$relatedItem = $site->getItemByName($relatedClass);

		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $this->getValue(),
			'readonly' => $this->getReadonly(),
			'required' => $this->getRequired(),
			'relatedClass' => $relatedItem->getNameId(),
		);

		return $scope;
	}

	public function getSearchView()
	{
		$scope = parent::getSearchView();

		$site = \App::make('site');

		$relatedClass = $this->getRelatedClass();
		$relatedItem = $site->getItemByName($relatedClass);

		$scope['relatedClass'] = $relatedItem->getNameId();

		return $scope;
	}

	public function isOneToOne()
	{
		return true;
	}

}
