<?php namespace LemonTree\Properties;

class VirtualProperty extends BaseProperty {

	public static function create($name)
	{
		return new self($name);
	}

	public function getRefresh()
	{
		return true;
	}

	public function setElement(ElementInterface $element)
	{
		$this->element = $element;

		$getter = $this->getter();

		$this->value = $element->$getter();

		return $this;
	}

	public function set()
	{
		return $this;
	}

	public function getElementSearchView()
	{
		return null;
	}

}
