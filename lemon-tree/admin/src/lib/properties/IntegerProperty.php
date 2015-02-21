<?php namespace LemonTree\Properties;

class IntegerProperty extends BaseProperty {

	public function __construct($name) {
		parent::__construct($name);

		$this->
		addRule('integer', 'Введите целое число');

		return $this;
	}

	public static function create($name)
	{
		return new self($name);
	}

	public function searchQuery($query)
	{
		$name = $this->getName();

		$from = \Input::get($name.'_from');
		$to = \Input::get($name.'_to');

		if (strlen($from)) {
			$from = str_replace(array(',', ' '), array('.', ''), $from);
			$query->where($name, '>=', (int)$from);
		}

		if (strlen($to)) {
			$to = str_replace(array(',', ' '), array('.', ''), $to);
			$query->where($name, '<=', (int)$to);
		}

		return $query;
	}

	public function searching()
	{
		$name = $this->getName();

		$from = \Input::get($name.'_from');
		$to = \Input::get($name.'_to');

		return strlen($from) || strlen($to)
			? true : false;
	}

	public function getSearchView()
	{
		$name = $this->getName();

		$from = \Input::get($name.'_from');
		$to = \Input::get($name.'_to');

		if ( ! mb_strlen($from)) $from = null;
		if ( ! mb_strlen($to)) $to = null;

		$value = null;

		if ($from) $value['from'] = $from;
		if ($to) $value['to'] = $to;

		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'value' => $value,
			'open' => $value !== null,
		);

		return $scope;
	}

}
