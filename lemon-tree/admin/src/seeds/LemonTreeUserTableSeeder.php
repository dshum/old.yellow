<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use LemonTree\Models\User;
use LemonTree\Models\Group;

class LemonTreeUserTableSeeder extends Seeder {

	public function run()
	{
		Model::unguard();

		DB::table('cytrus_users')->truncate();

		$user1 = User::create([
			'login' => 'magus',
			'password' => 'test',
			'email' => 'denis-shumeev@yandex.ru',
			'first_name' => 'Magus',
			'last_name' => 'III',
			'superuser' => true,
		]);

		$user2 = User::create([
			'login' => 'denis',
			'password' => 'pass',
			'email' => 'denis-shumeev@yandex.ru',
			'first_name' => 'Denis',
			'last_name' => 'Shumeev',
			'superuser' => false,
		]);

		$group1 = Group::find(1);

		$user2->addGroup($group1);

	}

}
