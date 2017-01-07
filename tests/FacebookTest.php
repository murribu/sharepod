<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Laravel\Socialite\Contracts\Factory as Socialite;

use App\SocialUser;
use App\User;

class FacebookTest extends TestCase
{
    use DatabaseTransactions;
    
    // Sources of help
    // https://stefanzweifel.io/posts/how-i-write-integration-tests-for-laravel-socialite-powered-apps
    // https://laracasts.com/lessons/mock-that-thang
    
    public function tearDown(){
        Mockery::close();
    }
    
    public function mockSocialiteFacade($email = 'foo@bar.com', $id = null)
    {
        $faker = Faker::create();
        if (!$id){
            $id = $faker->randomNumber(5);
        }
        $name = $faker->name;
        $url = $faker->url;
        
        $socialiteUser = Mockery::mock(Laravel\Socialite\Two\User::class);
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

        $provider = Mockery::mock(Laravel\Socialite\Two\FacebookProvider::class);
        $provider->shouldReceive('user')
            ->andReturn($socialiteUser);

        $stub = Mockery::mock(Socialite::class);
        $stub->shouldReceive('driver')->with('facebook')
            ->andReturn($provider);

        // Replace Socialite Instance with our mock
        $this->app->instance(Socialite::class, $stub);
    }
    
    public function test_create_a_new_user_from_facebook_login()
    {
        $faker = Faker::create();
        $userId = $faker->randomNumber(5);
        $email = $faker->email;
        
        $this->mockSocialiteFacade($email, $userId);

        $faker = Faker::create();
        $code = $faker->randomNumber();
        $state = $faker->randomNumber();
        $this->visit('auth/facebook/callback?code='.$code.'&state='.$state);

        $user = User::where('email', $email)->first();
        $this->assertNotEmpty($user, 'User was not created');
        $this->assertNotEmpty($user->facebook_user(), 'User was not linked to a Facebook SocialUser');

    }
    
    public function test_login_as_an_existing_facebook_user(){
        $faker = Faker::create();
        $userId = $faker->randomNumber(5);
        $email = $faker->email;
        
        $this->mockSocialiteFacade($email, $userId);
        $code = $faker->randomNumber();
        $state = $faker->randomNumber();

        $cb = $this->visit('auth/facebook/callback?code='.$code.'&state='.$state);
        $this->assertEquals('Login Redirect', $cb->crawler->filterXPath('//html/head/title')->text());
        $this->visit('user/current');
        
        $user1 = json_decode($this->response->getContent());
        $this->visit('logout');
        
        $this->visit('auth/facebook/callback?code='.$code.'&state='.$state);
        $this->assertEquals('Login Redirect', $cb->crawler->filterXPath('//html/head/title')->text());
        $this->visit('user/current');
        
        $user2 = json_decode($this->response->getContent());
        $this->visit('logout');
        
        $this->assertEquals($user1->id, $user2->id);
    }
    
    public function test_link_an_existing_user_to_facebook()
    {
        $faker = Faker::create();
        $email = $faker->email;
        $this->mockSocialiteFacade($email);
        
        $user = new User;
        $user->email = $email;
        $user->save();
        
        $code = $faker->randomNumber();
        $state = $faker->randomNumber();
        $this->actingAs($user)
            ->visit('auth/facebook/callback?code='.$code.'&state='.$state);
            
        $this->assertNotEmpty($user->facebook_user(), 'User was not linked to a Facebook SocialUser');
    }
}
