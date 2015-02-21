<?php namespace LemonTree\Properties;

class LinkProperty extends BaseProperty {

	protected $items = array();

	protected $rules = array();

	public static function create($name)
	{
		return new self($name);
	}

	public function getRefresh()
	{
		return true;
	}

	public function addItems()
	{
		$num = func_num_args();
		$args = func_get_args();

		if ($num < 1) return $this;

		if (is_array($args[0])) {
			foreach ($args[0] as $arg) {
				$this->items[$arg] = $arg;
			}
		} else {
			foreach ($args as $arg) {
				$this->items[$arg] = $arg;
			}
		}

		return $this;
	}

	public function getItems()
	{
		return $this->items;
	}

	public function element()
	{
		return Element::getByClassId($this->value);
	}

	public function getElementListView()
	{
		$element = $this->element();
		$item = $element ? $element->getItem() : null;
		$mainProperty = $item ? $item->getMainProperty() : null;

		$scope = array(
			'value' => $this->getValue(),
			'element' => $this->element(),
			'mainProperty' => $mainProperty,
		);

		try {
			$view = $this->getClassName().'.elementList';
			return \View::make('admin::properties.'.$view, $scope);
		} catch (\Exception $e) {}

		return null;
	}

	public function getEditView()
	{
		$element = $this->element();
		$item = $element ? $element->getItem() : null;
		$mainProperty = $item ? $item->getMainProperty() : null;

		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'item' => $this->getItem(),
			'value' => $this->getValue(),
			'element' => $this->element(),
			'readonly' => $this->getReadonly(),
			'required' => $this->getRequired(),
			'mainProperty' => $mainProperty,
		);

		return $scope;
	}

}
