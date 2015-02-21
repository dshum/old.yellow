<?php namespace LemonTree\Properties;

class RichtextProperty extends BaseProperty {

	protected $typograph = true;

	public static function create($name)
	{
		return new self($name);
	}

	public function setTypograph($typograph)
	{
		$this->typograph = $typograph;

		return $this;
	}

	public function getTypograph()
	{
		return $this->typograph;
	}

}
