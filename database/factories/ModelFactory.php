<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'slug' => App\User::findSlug(),
    ];
});

$factory->define(App\Episode::class, function(Faker\Generator $faker){
    return [
        'name' => $faker->name,
        'slug' => App\Episode::findSlug(),
        'show_id' => function() {
            return factory(App\Show::class)->create()->id;
        }
    ];
});

$factory->define(App\Show::class, function(Faker\Generator $faker){
    return [
        'name' => 'The '.$faker->name.' Podcast',
        'slug' => App\Show::findSlug(),
        'feed' => $faker->url
    ];
});

$factory->define(App\ArchivedEpisode::class, function(Faker\Generator $faker){
    return [
        'slug' => App\ArchivedEpisode::findSlug(),
        'episode_id' => function() {
            return factory(App\Episode::class)->create()->id;
        }
    ];
});

$factory->define(App\ArchivedEpisodeUser::class, function(Faker\Generator $faker){
    return [
        'archived_episode_id' => function() {
            return factory(App\ArchivedEpisode::class)->create()->id;
        },
        'user_id' => function() {
            return factory(App\User::class)->create()->id;
        },
        'active' => 1,
    ];
});

$factory->define(App\Playlist::class, function(Faker\Generator $faker){
    return [
        'slug' => App\Playlist::findSlug(),
        'name' => $faker->name,
        'description' => $faker->sentence,
        'user_id' => function() {
            return factory(App\User::class)->create(['verified' => 1])->id;
        }
    ];
});
