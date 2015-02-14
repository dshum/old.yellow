<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Good;

class GoodTableSeeder extends Seeder {

	public function run()
	{
		Model::unguard();

		DB::table('goods')->truncate();

		Good::create(array(
			'name' => 'Апельсины',
			'order' => 1,
			'url' => 'oranges',
			'supplier_price' => 100,
			'price' => 120,
			'shortcontent' => '',
			'fullcontent' => '',
			'category_id' => 1,
		));

		Good::create(array(
			'name' => 'Яблоки',
			'order' => 2,
			'url' => 'apples',
			'supplier_price' => 80,
			'price' => 100,
			'shortcontent' => '',
			'fullcontent' => '',
			'category_id' => 1,
		));

		Good::create(array(
			'name' => 'Груши',
			'order' => 3,
			'url' => 'peaches',
			'supplier_price' => 150,
			'price' => 180,
			'shortcontent' => '',
			'fullcontent' => '',
			'category_id' => 1,
		));

	}

}
