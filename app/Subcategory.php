<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use LemonTree\ElementInterface;
use LemonTree\ElementTrait;

class Subcategory extends Model implements ElementInterface {

	use ElementTrait;

	public function getHref()
	{
		$category = $this->category;

		return \URL::route('catalogue', array(
			'url1' => $category->url,
			'url2' => $this->url
		));
	}

	public function category()
	{
		return $this->belongsTo('Category', 'category_id');
	}

}
