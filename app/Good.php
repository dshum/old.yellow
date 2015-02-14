<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Good extends Model {

//	use LemonTree\ElementTrait;

	public function getHref()
	{
		$category = $this->category;

		return \URL::route('catalogue', array(
			'url1' => $category->url,
			'url2' => $this->url
		));
	}

	public function getFolderHash()
	{
		return substr(md5(rand()), 0, 2);
	}

	public function category()
	{
		return $this->belongsTo('Category', 'category_id');
	}

	public function subcategory()
	{
		return $this->belongsTo('Subcategory', 'subcategory_id');
	}

	public function color()
	{
		return $this->belongsTo('GoodColor', 'good_color_id');
	}

	public function size()
	{
		return $this->belongsTo('GooвЫшяу', 'good_size_id');
	}

}
