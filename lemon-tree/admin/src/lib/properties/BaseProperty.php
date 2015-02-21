<?php namespace LemonTree\Properties;

use LemonTree\Item;
use LemonTree\ElementInterface;

abstract class BaseProperty {

	protected $item = null;
	protected $name = null;
	protected $title = null;
	protected $class = null;

	protected $show = false;
	protected $required = false;
	protected $readonly = false;
	protected $hidden = false;
	protected $editable = false;

	protected $itemClass = null;
	protected $element = null;
	protected $value = null;

	protected $trashed = false;

	protected $rules = array();
	protected $messages = array();

	public function __construct($name)
	{
		$this->name = $name;
		$this->class = class_basename(get_class($this));

		return $this;
	}

	public function getClassName()
	{
		return class_basename(get_class($this));
	}

	public function setItem(Item $item)
	{
		$this->item = $item;

		$itemClass = $item->getName();

		$this->itemClass = new $itemClass;

		return $this;
	}

	public function getItem()
	{
		return $this->item;
	}

	public function getItemClass()
	{
		return $this->itemClass;
	}

	public function getItemName()
	{
		return $this->item->getName();
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

	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function isMainProperty()
	{
		return
			$this->item->getMainProperty() == $this->name
			? true
			: false;
	}

	public function setShow($show)
	{
		$this->show = $show;

		return $this;
	}

	public function getShow()
	{
		return $this->show;
	}

	public function setRequired($required)
	{
		$this->required = $required;

		if ($required) {
			$this->
			addRule('required', 'Поле обязательно к заполнению');
		}

		return $this;
	}

	public function getRequired()
	{
		return $this->required;
	}

	public function setReadonly($readonly)
	{
		$this->readonly = $readonly;

		return $this;
	}

	public function getReadonly()
	{
		return $this->readonly;
	}

	public function setHidden($hidden)
	{
		$this->hidden = $hidden;

		return $this;
	}

	public function getHidden()
	{
		return $this->hidden;
	}

	public function setEditable($editable)
	{
		$this->editable = $editable;

		return $this;
	}

	public function getEditable()
	{
		return $this->editable;
	}

	public function getRefresh()
	{
		return false;
	}

	public function setElement(ElementInterface $element)
	{
		$this->element = $element;

		$this->value = $element->{$this->getName()};
		$this->trashed = $element->trashed();

		return $this;
	}

	public function getElement()
	{
		return $this->element;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function isTrashed()
	{
		return $this->trashed;
	}

	public function searchQuery($query)
	{
		$name = $this->getName();

		$value = \Input::get($name);

		if ($value !== null) {
			$query->where($name, 'ilike', "%$value%");
		}

		return $query;
	}

	public function searching()
	{
		$name = $this->getName();

		$value = \Input::get($name);

		return $value !== null
			? true : false;
	}

	public function set($field = null)
	{
		if ( ! $field) $field = $this->getName();

		$name = $this->getName();

		$value = \Input::get($field);

		if ( ! mb_strlen($value)) $value = null;

		$this->element->$name = $value;

		return $this;
	}

	public function drop() {}

	public function getListView()
	{
		$element = $this->getElement();
		$classId = $element ? $element->getClassId() : null;

		$scope = array(
			'value' => $this->getValue(),
			'isMainProperty' => $this->isMainProperty(),
			'classId' => $classId,
			'trashed' => $this->isTrashed(),
		);

		return $scope;
	}

	public function getEditView()
	{
		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $this->getValue(),
			'readonly' => $this->getReadonly(),
		);

		return $scope;
	}

	public function getEditListView()
	{
		return $this->getListView();
	}

	public function getSearchView()
	{
		$value = \Input::get($this->getName());

		if ( ! mb_strlen($value)) $value = null;

		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $value,
			'isMainProperty' => $this->isMainProperty(),
			'open' => $value !== null,
		);

		return $scope;
	}

	public function setRules($rules)
	{
		$this->rules = $rules;

		return $this;
	}

	public function addRule($rule, $message = null)
	{
		$this->rules[$rule] = $message ?: $rule;

		return $this;
	}

	public function getRules()
	{
		return $this->rules;
	}

	public function isOneToOne()
	{
		return false;
	}

	protected function setter()
	{
		return 'set'.ucfirst($this->getName());
	}

	protected function getter()
	{
		return 'get'.ucfirst($this->getName());
	}

}
