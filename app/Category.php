<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

	public function getHref()
	{
		return \URL::route('catalogue', array('url' => $this->url));
	}

}
