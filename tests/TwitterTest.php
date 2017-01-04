<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Laravel\Socialite\Contracts\Factory as Socialite;

use App\SocialUser;
use App\User;

class TwitterTest extends TestCase
{
    use DatabaseTransactions;
    
    // Sources of help
    // https://stefanzweifel.io/posts/how-i-write-integration-tests-for-laravel-socialite-powered-apps
    // https://laracasts.com/lessons/mock-that-thang
    
    public function tearDown(){
        Mockery::close();
    }
    
    /**
     * Mock the Socialite Factory, so we can hijack the OAuth Request.
     * @param  string  $email
     * @param  string  $token
     * @param  int $id
     * @return void
     */
    public function mockSocialiteFacade($email = 'foo@bar.com', $token = 'foo', $tokenSecret = 'bar')
    {
        $faker = Faker::create();
        $id = $faker->randomNumber(5);
        
        $socialiteUser = Mockery::mock(Laravel\Socialite\One\User::class);
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
            'description' => $faker->paragraph(),
        ];

        $provider = Mockery::mock(Laravel\Socialite\One\TwitterProvider::class);
        $provider->shouldReceive('user')
            ->andReturn($socialiteUser);

        $stub = Mockery::mock(Socialite::class);
        $stub->shouldReceive('driver')->with('twitter')
            ->andReturn($provider);

        // Replace Socialite Instance with our mock
        $this->app->instance(Socialite::class, $stub);
    }
    
    public function test_create_a_new_user_from_twitter_login()
    {
        $this->mockSocialiteFacade('foo@bar.com');

        $faker = Faker::create();
        $oauth_token = $faker->randomNumber();
        $oauth_verifier = $faker->randomNumber();
        $this->visit('auth/twitter/callback?oauth_token='.$oauth_token.'&oauth_verifier='.$oauth_verifier);

        $user = User::where('email', 'foo@bar.com')->first();
        $this->assertNotEmpty($user, 'User was not created');
        $this->assertNotEmpty($user->twitter_user(), 'User was not linked to a Twitter SocialUser');

    }
    
    public function test_link_an_existing_user_to_twitter()
    {
        $faker = Faker::create();
        $email = $faker->email;
        $this->mockSocialiteFacade($email);
        
        $user = new User;
        $user->email = $email;
        $user->save();
        $oauth_token = $faker->randomNumber();
        $oauth_verifier = $faker->randomNumber();
        $this->actingAs($user)
            ->visit('auth/twitter/callback?oauth_token='.$oauth_token.'&oauth_verifier='.$oauth_verifier);
            
        $this->assertNotEmpty($user->twitter_user(), 'User was not linked to a Twitter SocialUser');
        
        
    }
}
