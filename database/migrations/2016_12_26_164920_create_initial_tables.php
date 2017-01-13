<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInitialTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->nullable()->unique();
            $table->string('name')->nullable();
            $table->string('feed')->unique();
            $table->string('url')->nullable();
            $table->boolean('active')->default(true);
            $table->string('img_url')->nullable();
            $table->longText('description')->nullable();
            $table->integer('owner_id')->unsigned()->nullable();
            $table->foreign('owner_id')->references('id')->on('users');
            $table->timestamps();
        });
        Schema::create('episodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('shows');
            $table->string('guid')->nullable();
            $table->string('name')->nullable();
            $table->index('show_id', 'guid');
            $table->longText('description')->nullable();
            $table->integer('duration')->nullable();
            $table->boolean('explicit')->default(false);
            $table->integer('filesize')->nullable();
            $table->string('img_url')->nullable();
            $table->string('link')->nullable();
            $table->integer('pubdate')->nullable();
            $table->string('url')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('twitter_calls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('function_call');
            $table->longText('parameters')->nullable();
            $table->longText('response')->nullable();
            $table->timestamps();
        });
        Schema::create('social_friends', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('social_user_id')->index();
            $table->enum('type', ['twitter', 'facebook']);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('friends', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('friend_id')->unsigned();
            $table->foreign('friend_id')->references('id')->on('users');
            $table->timestamps();
        });
        Schema::create('likes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('fk')->unsigned()->index();
            $table->integer('ordering')->nullable()->index();
            $table->enum('type', ['episode', 'show', 'playlist']);
            $table->timestamps();
        });
        Schema::create('social_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name')->nullable();
            $table->string('social_id')->index();
            $table->string('screen_name')->nullable();
            $table->string('description')->nullable();
            $table->string('url')->nullable();
            $table->string('utc_offset')->nullable();
            $table->string('profile_background_image_url')->nullable();
            $table->string('profile_image_url')->nullable();
            $table->string('oauth_token')->nullable();
            $table->string('oauth_verifier')->nullable();
            $table->string('token')->nullable();
            $table->string('token_secret')->nullable();
            $table->string('nickname')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->string('avatar_original')->nullable();
            $table->string('gender', 1)->nullable();
            $table->string('code', 1024)->nullable();
            $table->string('state')->nullable();
            $table->enum('type', ['twitter', 'facebook']);
            $table->unique(['social_id', 'type']);
            $table->timestamps();
        });
        Schema::create('playlists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('type', ['mixtape', 'recommendations']);
            $table->timestamps();
        });
        Schema::create('playlist_episodes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('playlist_id')->unsigned();
            $table->foreign('playlist_id')->references('id')->on('playlists');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('episode_id')->unsigned();
            $table->foreign('episode_id')->references('id')->on('episodes');
            $table->integer('ordering')->nullable();
            $table->timestamps();
        });
        Schema::create('hitcounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('request')->index();
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('ip')->index();
            $table->integer('fk')->unsigned()->index()->nullable();
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->integer('twitter_user_id')->index()->nullable();
            $table->integer('facebook_user_id')->index()->nullable();
            $table->string('slug')->unique();
        });
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('twitter_user_id');
            $table->dropColumn('facebook_user_id');
            $table->dropColumn('slug');
        });
        Schema::dropIfExists('hitcounts');
        Schema::dropIfExists('playlist_episodes');
        Schema::dropIfExists('playlists');
        Schema::dropIfExists('playlist_types');
        Schema::dropIfExists('social_users');
        Schema::dropIfExists('likes');
        Schema::dropIfExists('friends');
        Schema::dropIfExists('social_friends');
        Schema::dropIfExists('twitter_calls');
        Schema::dropIfExists('episodes');
        Schema::dropIfExists('shows');
    }
}
