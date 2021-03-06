<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlidesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('slides', function(Blueprint $table) {
			$table->engine = 'InnoDB';
      		$table->increments('id');
			$table->integer('slideshow_id');
			$table->string('title');
      		$table->string('description')->nullable();
			$table->string('link')->nullable();
			$table->string('image');
			$table->integer('rankorder');
			$table->boolean('active');
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
		Schema::drop('slides');
	}
}
