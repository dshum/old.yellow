<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use LemonTree\ElementInterface;
use LemonTree\ElementTrait;

class Category extends Model implements ElementInterface {

	use ElementTrait;

	public function getHref()
	{
		return \URL::route('catalogue', array('url' => $this->url));
	}

}
