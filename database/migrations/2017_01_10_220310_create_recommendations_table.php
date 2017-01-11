<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->integer('recommender_id')->unsigned();
            $table->foreign('recommender_id')->references('id')->on('users');
            $table->integer('recommendee_id')->unsigned();
            $table->foreign('recommendee_id')->references('id')->on('users');
            $table->integer('episode_id')->unsigned();
            $table->foreign('episode_id')->references('id')->on('episodes');
            $table->enum('action', ['viewed', 'accepted', 'rejected'])->nullable();
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
        Schema::dropIfExists('recommendations');
    }
}
