<?php namespace LemonTree\Models;

use Illuminate\Database\Eloquent\Model;
use LemonTree\Site;
use LemonTree\Item;
use LemonTree\ElementInterface;

class Group extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'cytrus_groups';
	protected $pivotTable = 'cytrus_users_groups';

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
		\Cache::tags('Group')->flush();

		\Cache::forget("getGroupById({$this->id})");
	}

	public function users()
	{
		return $this->belongsToMany('LemonTree\Models\User', $this->pivotTable);
	}

	public function hasAccess($name)
	{
		return $this->getPermission($name) ? true : false;
	}

	public function getUnserializedPermissions()
	{
		try {
			return unserialize($this->permissions);
		} catch (\Exception $e) {}

		return null;
	}

	public function getPermission($name)
	{
		$unserializedPermissions = $this->getUnserializedPermissions();

		return
			isset($unserializedPermissions[$name])
			? $unserializedPermissions[$name]
			: null;
	}

	public function setPermission($name, $value)
	{
		$unserializedPermissions = $this->getUnserializedPermissions();

		$unserializedPermissions[$name] = $value;

		$permissions = serialize($unserializedPermissions);

		$this->permissions = $permissions;

		return $this;
	}

	public function itemPermissions()
	{
		return $this->hasMany('LemonTree\Models\GroupItemPermission');
	}

	public function elementPermissions()
	{
		return $this->hasMany('LemonTree\Models\GroupElementPermission');
	}

	public function getItemPermission($class)
	{
		return \Cache::tags("GroupItemPermission.{$this->id}")->rememberForever(
			"Group.{$this->id}.itemPermission.$class",
			function () use ($class) {
				return $this->itemPermissions()->where('class', $class)->first();
			}
		);
	}

	public function getElementPermission($classId)
	{
		return \Cache::tags("GroupElementPermission.{$this->id}")->rememberForever(
			"Group.{$this->id}.elementPermission.$classId",
			function () use ($classId) {
				return $this->elementPermissions()->where('class_id', $classId)->first();
			}
		);
	}

	public function getItemAccess(Item $item)
	{
		$itemPermission = $this->getItemPermission($item->getName());

		if ($itemPermission) return $itemPermission->permission;

		return $this->default_permission;
	}

	public function getElementAccess(ElementInterface $element)
	{
		$elementPermission = $this->getElementPermission($element->getClassId());

		if ($elementPermission) return $elementPermission->permission;

		$itemPermission = $this->getItemPermission($element->getClass());

		if ($itemPermission) return $itemPermission->permission;

		return $this->default_permission;
	}

}
