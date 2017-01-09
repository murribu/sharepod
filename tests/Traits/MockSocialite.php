<?php
namespace Tests\Traits;

use \Illuminate\Foundation\Testing\DatabaseTransactions;
use \Faker\Factory as Faker;

use \Laravel\Socialite\Contracts\Factory as Socialite;

trait MockSocialite
{
    // Sources of help
    // https://stefanzweifel.io/posts/how-i-write-integration-tests-for-laravel-socialite-powered-apps
    // https://laracasts.com/lessons/mock-that-thang
    
    public function tearDown(){
        \Mockery::close();
    }
    
    public function mockSocialiteFacadeFacebook($email = 'foo@bar.com', $id = null)
    {
        $faker = Faker::create();
        if (!$id){
            $id = $faker->randomNumber(5);
        }
        $name = $faker->name;
        $url = $faker->url;
        
        $socialiteUser = \Mockery::mock(\Laravel\Socialite\Two\User::class);
        $socialiteUser->token = $faker->randomNumber(5);
        $socialiteUser->id = $id;
        $socialiteUser->nickname = $faker->userName;
        $socialiteUser->name = $name;
        $socialiteUser->email = $email;
        $socialiteUser->avatar = $faker->imageUrl();
        $socialiteUser->avatar_original = $faker->imageUrl();
        $socialiteUser->profileUrl = $url;
        $socialiteUser->user = [
            'name' => $name,
            'email' => $email,
            'gender' => $faker->word,
            'verified' => true,
            'link' => $url,
            'id' => $id,
        ];

        $provider = \Mockery::mock(\Laravel\Socialite\Two\FacebookProvider::class);
        $provider->shouldReceive('user')
            ->andReturn($socialiteUser);

        $stub = \Mockery::mock(Socialite::class);
        $stub->shouldReceive('driver')->with('facebook')
            ->andReturn($provider);

        // Replace Socialite Instance with our mock
        $this->app->instance(Socialite::class, $stub);
    }
    
    public function mockSocialiteFacadeTwitter($email = 'foo@bar.com', $id = null, $token = 'foo', $tokenSecret = 'bar')
    {
        $faker = Faker::create();
        if (!$id){
            $id = $faker->randomNumber(5);
        }
        
        $socialiteUser = \Mockery::mock(\Laravel\Socialite\One\User::class);
        $socialiteUser->token = $token;
        $socialiteUser->tokenSecret = $tokenSecret;
        $socialiteUser->email = $email;
        $socialiteUser->nickname = $faker->userName;
        $socialiteUser->avatar = $faker->imageUrl();
        $socialiteUser->avatar_original = $faker->imageUrl();
        $socialiteUser->id = $id;
        $socialiteUser->user = [
            'id_str' => $id,
            'followers_count' => $faker->randomNumber(),
            'friends_count' => $faker->randomNumber(),
            'utc_offset' => '-21600',
            'time_zone' => $faker->timezone,
            'profile_background_color' => $faker->randomNumber(6),
            'profile_background_image_url' => $faker->imageUrl(),
            'profile_image_url' => $faker->imageUrl(),
            'profile_banner_url' => $faker->imageUrl(), //probably should be https, but whatever
            'url' => $faker->url,
            'location' => $faker->city,
            'description' => $faker->sentence(),
        ];

        $provider = \Mockery::mock(\Laravel\Socialite\One\TwitterProvider::class);
        $provider->shouldReceive('user')
            ->andReturn($socialiteUser);

        $stub = \Mockery::mock(Socialite::class);
        $stub->shouldReceive('driver')->with('twitter')
            ->andReturn($provider);

        // Replace Socialite Instance with our mock
        $this->app->instance(Socialite::class, $stub);
    }
}