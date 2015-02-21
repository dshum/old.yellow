<?php namespace LemonTree\Properties;

class TextareaProperty extends BaseProperty {

	public static function create($name)
	{
		return new self($name);
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
