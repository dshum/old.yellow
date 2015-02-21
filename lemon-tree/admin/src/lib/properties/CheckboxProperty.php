<?php namespace LemonTree\Properties;

use LemonTree\ElementInterface;

class CheckboxProperty extends BaseProperty {

	public static function create($name)
	{
		return new self($name);
	}

	public function setElement(ElementInterface $element)
	{
		$this->element = $element;

		$value = $element->{$this->getName()};

		$this->value = $value ? true : false;

		return $this;
	}

	public function searchQuery($query)
	{
		$name = $this->getName();

		$value = \Input::get($name);

		if ($value === 'true') {
			$query->where($name, 1);
		} elseif ($value === 'false') {
			$query->where($name, 0);
		}

		return $query;
	}

	public function searching()
	{
		$name = $this->getName();

		$value = \Input::get($name);

		return $value === 'true' || $value === 'false'
			? true : false;
	}

	public function set($field = null)
	{
		if ( ! $field) $field = $this->getName();

		$name = $this->getName();

		$value = \Input::has($field) && \Input::get($field)
			? true : false;

		$this->element->$name = $value;

		return $this;
	}

	public function getBrowseEditView()
	{
		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $this->getValue(),
			'element' => $this->getElement(),
			'readonly' => $this->getReadonly(),
		);

		try {
			$view = $this->getClassName().'.browseEdit';
			return \View::make('admin::properties.'.$view, $scope);
		} catch (\Exception $e) {}

		return null;
	}

}
