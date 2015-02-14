<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subcategories', function ($table) {
			$table->increments('id');
			$table->string('name');
			$table->integer('order');
			$table->string('url');
			$table->string('title')->nullable();
			$table->mediumText('fullcontent')->nullable();
			$table->string('image')->nullable();
			$table->boolean('hide')->nullable();
			$table->integer('category_id')->unsigned()->index();
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
		Schema::drop('subcategories');
	}

}
