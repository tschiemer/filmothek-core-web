<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilmTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('films',function($table){
                    $table->increments('id');
                    
                    $table->string('nr');
                    $table->string('title');
                    $table->string('artist');
                    $table->string('country');
                    $table->string('year');
                    $table->string('length');
                    $table->string('technique');
                    
                    $table->string('poster')->nullable()->default(false);
                    $table->string('video')->nullable()->default(false);
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('films');
	}

}
