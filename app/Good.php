<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use LemonTree\ElementInterface;
use LemonTree\ElementTrait;

class Good extends Model implements ElementInterface {

	use ElementTrait;

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
		return $this->belongsTo('App\Category', 'category_id');
	}

	public function subcategory()
	{
		return $this->belongsTo('App\Subcategory', 'subcategory_id');
	}

	public function color()
	{
		return $this->belongsTo('App\GoodColor', 'good_color_id');
	}

	public function size()
	{
		return $this->belongsTo('App\GoodSize', 'good_size_id');
	}

}
