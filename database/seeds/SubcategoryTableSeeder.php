<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Subcategory;

class SubcategoryTableSeeder extends Seeder {

	public function run()
	{
		Model::unguard();

		DB::table('subcategories')->truncate();

	}

}
