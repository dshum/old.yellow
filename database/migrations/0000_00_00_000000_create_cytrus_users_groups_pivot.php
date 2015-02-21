<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCytrusUsersGroupsPivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cytrus_users_groups', function($table)
		{
			$table->integer('user_id')->unsigned();
			$table->integer('group_id')->unsigned();
			$table->engine = 'InnoDB';
			$table->primary(array('user_id', 'group_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cytrus_users_groups');
	}

}
