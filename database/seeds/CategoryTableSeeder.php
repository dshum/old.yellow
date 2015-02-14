<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Category;

class CategoryTableSeeder extends Seeder {

	public function run()
	{
		Model::unguard();

		DB::table('categories')->truncate();

		Category::create(array(
			'name' => 'Фрукты',
			'order' => 1,
			'url' => 'fruits',
			'title' => 'Фрукты',
			'shortcontent' => '',
			'fullcontent' => '',
		));

		Category::create(array(
			'name' => 'Ягоды',
			'order' => 2,
			'url' => 'berries',
			'title' => 'Ягоды',
			'shortcontent' => '',
			'fullcontent' => '',
		));

		Category::create(array(
			'name' => 'Овощи',
			'order' => 3,
			'url' => 'vegetables',
			'title' => 'Овощи',
			'shortcontent' => '',
			'fullcontent' => '',
		));

	}

}
