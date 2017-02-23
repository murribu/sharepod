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
            $table->boolean('explicit')->nullable();
            $table->integer('filesize')->unsigned()->nullable();
            $table->string('img_url')->nullable();
            $table->string('link')->nullable();
            $table->integer('pubdate')->nullable();
            $table->string('url')->nullable();
            $table->enum('status_code', ['200', '401', '403', '404', '500'])->nullable();
            $table->string('status_message')->nullable();
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
        Schema::dropIfExists('archived_episode_requests_log');
        Schema::dropIfExists('archived_episode_requests');
        Schema::dropIfExists('archived_episodes');
    }
}
