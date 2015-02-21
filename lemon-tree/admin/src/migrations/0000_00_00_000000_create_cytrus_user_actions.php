<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCytrusUserActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cytrus_user_actions', function ($table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->string('action_type')->index();
			$table->string('comments');
			$table->string('url');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cytrus_user_actions');
	}

}
