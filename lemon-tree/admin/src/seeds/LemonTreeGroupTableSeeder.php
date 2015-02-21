<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use LemonTree\Models\Group;

class LemonTreeGroupTableSeeder extends Seeder {

	public function run()
	{
		Model::unguard();

		DB::table('cytrus_users_groups')->truncate();

		DB::table('cytrus_groups')->truncate();

		Group::create([
			'name' => 'Системные пользователи',
			'default_permission' => 'delete',
			'permissions' => serialize([
				'admin' => 1,
			]),
		]);

		Group::create([
			'name' => 'Администраторы',
			'default_permission' => 'delete',
			'permissions' => serialize([
				'admin' => 0,
			]),
		]);

		Group::create([
			'name' => 'Модераторы',
			'default_permission' => 'deny',
			'permissions' => serialize([
				'admin' => 0,
			]),
		]);

	}

}
