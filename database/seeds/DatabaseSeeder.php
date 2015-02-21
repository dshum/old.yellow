<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('CategoryTableSeeder');
		$this->call('SubcategoryTableSeeder');
		$this->call('GoodTableSeeder');
		$this->call('SectionTableSeeder');
		$this->call('ServiceSectionTableSeeder');
		$this->call('SiteSettingsTableSeeder');
	}

}
