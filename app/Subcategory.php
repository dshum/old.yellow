<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model {

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
