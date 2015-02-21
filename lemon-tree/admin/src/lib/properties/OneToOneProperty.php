<?php namespace LemonTree\Properties;

use LemonTree\Item;
use LemonTree\ElementInterface;

class OneToOneProperty extends BaseProperty {

	const RESTRICT = 1;
	const CASCADE = 2;
	const SETNULL = 3;

	protected $relatedClass = null;
	protected $deleting = self::RESTRICT;
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

	public function setDeleting($deleting)
	{
		if (in_array($deleting, array(
			self::RESTRICT,
			self::CASCADE,
			self::SETNULL,
		))) {
			$this->deleting = $deleting;
		}

		return $this;
	}

	public function getDeleting()
	{
		return $this->deleting;
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

		try {
			$this->value = $relatedClass && $id
				? $relatedClass::find($id) : null;
		} catch (\Exception $e) {}

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

	public function getElementMoveView()
	{
		$site = \App::make('site');

		$relatedClass = $this->getRelatedClass();
		$relatedItem = $site->getItemByName($relatedClass);

		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $this->getValue(),
			'element' => $this->getElement(),
			'readonly' => $this->getReadonly(),
			'required' => $this->getRequired(),
			'relatedClass' => $relatedItem->getNameId(),
			'mainProperty' => $relatedItem->getMainProperty(),
		);

		if ($this->getBinds()) {
			$treeView = \App::make('LemonTree\TreeController')->show1($this);
			$scope['treeView'] = $treeView ? $treeView : '';
		}

		try {
			$view = $this->getClassName().'.elementMove';
			return \View::make('admin::properties.'.$view, $scope);
		} catch (\Exception $e) {}

		return null;
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
