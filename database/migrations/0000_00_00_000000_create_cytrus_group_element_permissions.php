<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCytrusGroupElementPermissions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cytrus_group_element_permissions', function ($table) {
			$table->increments('id');
			$table->integer('group_id')->unsigned()->index();
			$table->string('class_id')->index();
			$table->string('permission');
			
			$table->unique(array('group_id', 'class_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cytrus_group_element_permissions');
	}

}
