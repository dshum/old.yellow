<?php namespace LemonTree\Properties;

use Carbon\Carbon;
use LemonTree\ElementInterface;

class DatetimeProperty extends BaseProperty {

	protected static $format = 'Y-m-d H:i:s';

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
				'time' => $this->value->toTimeString(),
				'hour' => $this->value->hour,
				'minute' => $this->value->minute,
				'second' => $this->value->second,
				'human' => $this->value->format('d.m.Y, H:i:s')
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
		$name = $this->getName();

		$from = \Input::get($name.'_from');
		$to = \Input::get($name.'_to');

		try {
			$from = Carbon::createFromFormat('Y-m-d', $from);
		} catch (\Exception $e) {
			$from = null;
		}

		try {
			$to = Carbon::createFromFormat('Y-m-d', $to);
		} catch (\Exception $e) {
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
