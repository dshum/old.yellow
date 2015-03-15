<?php namespace LemonTree\Models;

use Illuminate\Database\Eloquent\Model;
use LemonTree\Element;

class Favorite extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'cytrus_favorites';

	public static function boot()
	{
		parent::boot();

		static::created(function($element) {
			$element->flush();
		});

		static::saved(function($element) {
			$element->flush();
		});

		static::deleted(function($element) {
			$element->flush();
		});
    }

	public function flush()
	{
		\Cache::tags('Favorite')->flush();
	}

	public function getElement()
	{
		if ( ! $this->class_id) return null;

		return \Cache::rememberForever(
			'Favorite.'.$this->class_id,
			function() {
				return Element::getWithTrashedByClassId($this->class_id);
			}
		);
	}

}
