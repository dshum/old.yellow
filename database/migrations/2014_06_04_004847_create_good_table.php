<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('goods', function ($table) {
			$table->increments('id');
			$table->string('name');
			$table->integer('order');
			$table->string('url');
			$table->double('supplier_price')->nullable();
			$table->double('price')->nullable();
			$table->double('old_price')->nullable();
			$table->string('image')->nullable();
			$table->string('title')->nullable();
			$table->string('meta_keywords')->nullable();
			$table->text('meta_description')->nullable();
			$table->text('shortcontent')->nullable();
			$table->mediumText('fullcontent')->nullable();
			$table->boolean('special')->nullable();
			$table->boolean('novelty')->nullable();
			$table->boolean('hide')->nullable();
			$table->boolean('absent')->nullable();
			$table->integer('category_id')->unsigned()->index();
			$table->integer('subcategory_id')->unsigned()->nullable()->default(null)->index();
			$table->integer('good_color_id')->unsigned()->nullable()->default(null)->index();
			$table->integer('good_size_id')->unsigned()->nullable()->default(null)->index();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('goods');
	}

}
