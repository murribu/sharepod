<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchivedEpisodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archived_episode_results', function(Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->boolean('success');
            $table->timestamps();
        });
        Schema::create('archived_episodes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('episode_id')->unsigned()->nullable();
            $table->foreign('episode_id')->references('id')->on('episodes');
            $table->string('slug')->unique();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('explicit')->nullable();
            $table->integer('filesize')->unsigned()->nullable();
            $table->string('img_url')->nullable();
            $table->string('link')->nullable();
            $table->integer('pubdate')->nullable();
            $table->string('url')->nullable();
            $table->string('result_slug')->index()->nullable();
            $table->foreign('result_slug')->references('slug')->on('archived_episode_results');
            $table->timestamp('processed_at')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('archived_episode_users', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('archived_episode_id')->unsigned();
            $table->foreign('archived_episode_id')->references('id')->on('archived_episodes');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->boolean('active')->default(false)->index();
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
        Schema::dropIfExists('archived_episode_users');
        Schema::dropIfExists('archived_episodes');
        Schema::dropIfExists('archived_episode_results');
    }
}
