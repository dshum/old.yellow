<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UserTableSeeder extends Seeder {

	public function run()
	{
		Model::unguard();

		DB::table('users')->truncate();

		User::create(array(
			'email' => 'denis-shumeev@yandex.ru',
			'password' => Hash::make('qwerty'),
			'fio' => 'Денис Шумеев',
			'phone' => '+7 926 3937226',
			'activated' => true,
		));

	}

}
