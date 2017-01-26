<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePodcatcherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('podcatchers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('url');
            $table->string('url_register_feed');
            $table->integer('hits')->nullable();
            $table->timestamps();
        });
        Schema::create('podcatcher_platforms', function(Blueprint $table) {
            $table->increments('id');
            $table->string('platform');
            $table->integer('podcatcher_id')->unsigned();
            $table->foreign('podcatcher_id')->references('id')->on('podcatchers');
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
        Schema::dropIfExists('podcatcher_platforms');
        Schema::dropIfExists('podcatchers');
    }
}
