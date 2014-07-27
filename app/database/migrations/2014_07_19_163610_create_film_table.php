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
                    $table->string('title_en')->nullable()->default(null);
                    $table->string('artist')->nullable()->default(null);
                    $table->string('country')->nullable()->default(null);
                    $table->string('year')->nullable()->default(null);
                    $table->string('length')->nullable()->default(null);
                    $table->string('technique')->nullable()->default(null);
                    
                    $table->string('poster')->nullable()->default(null);
                    $table->string('video')->nullable()->default(null);
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
