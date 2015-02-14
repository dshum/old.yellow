<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\SiteSettings;

class SiteSettingsTableSeeder extends Seeder {

	public function run()
	{
		Model::unguard();

		DB::table('site_settings')->truncate();

		SiteSettings::create(array(
			'name' => 'Настройки сайта',
			'title' => 'Фрукты и Овощи',
			'meta_keywords' => 'фрукты, ягоды, овощи, Yellow App',
			'meta_description' => 'Фрукты и Овощи, Yellow App',
		));

	}

}
