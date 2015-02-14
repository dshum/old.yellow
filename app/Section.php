<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model {

	public function getHref()
	{
		return URL::route($this->url);
	}

}
