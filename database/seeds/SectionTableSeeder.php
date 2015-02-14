<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Section;

class SectionTableSeeder extends Seeder {

	public function run()
	{
		Model::unguard();

		DB::table('sections')->truncate();

		Section::create(array(
			'name' => 'Доставка',
			'order' => 1,
			'url' => 'delivery',
			'title' => 'Доставка',
			'h1' => 'Доставка',
			'shortcontent' => '',
			'fullcontent' => '<p>Доставка по Москве осуществляется на следующий день после заказа.</p>',
		));

		Section::create(array(
			'name' => 'Способы оплаты',
			'order' => 2,
			'url' => 'payments',
			'title' => 'Способы оплаты',
			'h1' => 'Способы оплаты',
			'shortcontent' => '',
			'fullcontent' => '<p>Способы оплаты:</p>
<p>
<ul>
<li>безналичный расчет;</li>
<li>квитанция в банке;</li>
<li>пластиковая карта;</li>
<li>терминалы оплаты QIWI;</li>
<li>Яндекс.Деньги;</li>
<li>Webmoney.</li>
</ul>
</p>',
		));

		Section::create(array(
			'name' => 'Контактная информация',
			'order' => 3,
			'url' => 'contacts',
			'title' => 'Контактная информация',
			'h1' => 'Контактная информация',
			'shortcontent' => '',
			'fullcontent' => '<p>Мы находимся по адресу: Москва, ул. Летчика Бабушкина, д. 125</p>
<p>Телефон: +7 495 1234567</p>',
		));

	}

}
