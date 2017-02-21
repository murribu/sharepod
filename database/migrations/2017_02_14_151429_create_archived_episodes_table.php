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
        Schema::create('archived_episodes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('episode_id')->unsigned()->nullable();
            $table->foreign('episode_id')->references('id')->on('episodes');
            $table->string('slug')->unique();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('explicit')->default(false);
            $table->integer('filesize')->unsigned();
            $table->string('img_url')->nullable();
            $table->string('link')->nullable();
            $table->integer('pubdate')->nullable();
            $table->string('url')->nullable();
            $table->boolean('active')->default(false);
            $table->enum('status_code', ['200', '401', '403', '404', '500'])->nullable();
            $table->string('message');
            $table->timestamps();
        });
        Schema::create('archived_episode_users', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('archived_episode_id')->unsigned();
            $table->foreign('archived_episode_id')->references('id')->on('archived_episode_id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('archived_episode_requests_log');
        Schema::dropIfExists('archived_episode_requests');
        Schema::dropIfExists('archived_episodes');
    }
}
