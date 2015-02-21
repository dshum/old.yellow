<?php namespace LemonTree\Properties;

class BinaryProperty extends BaseProperty {

	protected $maxSize = 16384;

	public function __construct($name) {
		parent::__construct($name);

		$this->
		addRule('max:'.$this->maxSize, 'Максимальный размер файла: '.$this->maxSize.' Кб');

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

	public function setMaxSize($maxSize)
	{
		$this->maxSize = $maxSize;

		return $this;
	}

	public function getMaxSize()
	{
		return $this->maxSize;
	}

	public function set()
	{
		$name = $this->getName();

		if (\Input::hasFile($name)) {
			$file = \Input::file($name);
			if ($file->isValid()) {
				$data = file_get_contents($file->getRealPath());
				$this->element->$name = $data;
				unlink($file->getRealPath());
			}
		} elseif (\Input::get($name.'_drop')) {
			$this->element->$name = null;
		}

		return $this;
	}

	public function getEditView()
	{
		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $this->getValue(),
			'readonly' => $this->getReadonly(),
			'maxFilesize' => $this->getMaxSize(),
		);

		return $scope;
	}

	public function getElementSearchView()
	{
		return null;
	}

}
