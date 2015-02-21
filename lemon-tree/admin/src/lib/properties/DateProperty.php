<?php namespace LemonTree\Properties;

use Carbon\Carbon;
use LemonTree\ElementInterface;

class DateProperty extends BaseProperty {

	protected static $format = 'Y-m-d';

	protected $fillNow = false;

	public function __construct($name) {
		parent::__construct($name);

		$this->
		addRule('date_format:"'.static::$format.'"', 'Недопустимый формат даты');

		return $this;
	}

	public static function create($name)
	{
		return new self($name);
	}

	public function setFillNow($fillNow)
	{
		$this->fillNow = $fillNow;

		return $this;
	}

	public function getFillNow()
	{
		return $this->fillNow;
	}

	public function setElement(ElementInterface $element)
	{
		parent::setElement($element);

		if (is_string($this->value)) {
			try {
				$this->value = Carbon::createFromFormat($this->format, $this->value);
			} catch (\Exception $e) {}
		}

		if ( ! $this->value && $this->getFillNow()) {
			$this->value = Carbon::now();
		}

		if ($this->value) {
			$this->value = [
				'value' => $this->value->format(static::$format),
				'date' => $this->value->toDateString(),
				'human' => $this->value->format('d.m.Y')
			];
		}

		return $this;
	}

	public function searchQuery($query)
	{
		$name = $this->getName();

		$from = \Input::get($name.'_from');
		$to = \Input::get($name.'_to');

		if ($from) {
			try {
				$from = Carbon::createFromFormat('Y-m-d', $from);
				$query->where($name, '>=', $from->format('Y-m-d'));
			} catch (\Exception $e) {}
		}

		if ($to) {
			try {
				$to = Carbon::createFromFormat('Y-m-d', $to);
				$query->where($name, '<=', $to->format('Y-m-d'));
			} catch (\Exception $e) {}
		}

		return $query;
	}

	public function searching()
	{
		$name = $this->getName();

		$from = \Input::get($name.'_from');
		$to = \Input::get($name.'_to');

		return $from || $to
			? true : false;
	}

	public function getElementSearchView()
	{
		$from = \Input::get($this->getName().'_from');
		$to = \Input::get($this->getName().'_to');

		try {
			$from = Carbon::createFromFormat('Y-m-d', $from);
			$to = Carbon::createFromFormat('Y-m-d', $to);
		} catch (\Exception $e) {
			$from = null;
			$to = null;
		}

		$scope = array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
			'from' => $from,
			'to' => $to,
		);

		try {
			$view = $this->getClassName().'.elementSearch';
			return \View::make('admin::properties.'.$view, $scope);
		} catch (\Exception $e) {}

		return null;
	}

}
