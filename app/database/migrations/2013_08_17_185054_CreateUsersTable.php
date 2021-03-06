<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table) {
			$table->increments('id');
			$table->string('ltid')->unique();
			$table->string('lastname');
			$table->string('firstname');
			$table->string('phone')->nullable();
			$table->string('email')->nullable();
			$table->date('birth')->nullable();
			$table->enum('lang', array('nor','eng'))->default('nor');
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
		Schema::drop('users');
	}

}