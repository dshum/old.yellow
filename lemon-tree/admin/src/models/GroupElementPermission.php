<?php namespace LemonTree\Models;

use Illuminate\Database\Eloquent\Model;

class GroupElementPermission extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'cytrus_group_element_permissions';

	public $timestamps = false;

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
		\Cache::tags('GroupElementPermission.'.$this->group_id)->flush();
	}

}
