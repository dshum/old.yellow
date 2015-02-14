<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\ServiceSection;

class ServiceSectionTableSeeder extends Seeder {

	public function run()
	{
		Model::unguard();

		DB::table('service_sections')->truncate();

		ServiceSection::create(array(
			'name' => 'Справочники',
			'order' => 1,
		));

		ServiceSection::create(array(
			'name' => 'Заказы',
			'order' => 2,
		));

		ServiceSection::create(array(
			'name' => 'Покупатели',
			'order' => 3,
		));

		ServiceSection::create(array(
			'name' => 'Расходы',
			'order' => 4,
		));

		ServiceSection::create(array(
			'name' => 'Счетчики',
			'order' => 5,
		));

		ServiceSection::create(array(
			'name' => 'Инструменты',
			'order' => 6,
		));

		ServiceSection::create(array(
			'name' => 'Статистика',
			'order' => 7,
		));

		ServiceSection::create(array(
			'name' => 'Выручка',
			'order' => 8,
			'service_section_id' => 7,
		));

		ServiceSection::create(array(
			'name' => 'Цвета товаров',
			'order' => 9,
			'service_section_id' => 1,
		));

		ServiceSection::create(array(
			'name' => 'Размеры товаров',
			'order' => 10,
			'service_section_id' => 1,
		));

	}

}
