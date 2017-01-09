<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Tests\Traits\MockSocialite;
use Laravel\Socialite\Contracts\Factory as Socialite;

use App\SocialUser;
use App\User;

class TwitterTest extends TestCase
{
    use DatabaseTransactions;
    use MockSocialite;
    
    public function test_create_a_new_user_from_twitter_login()
    {
        $faker = Faker::create();
        $userId = $faker->randomNumber(5);
        
        $this->mockSocialiteFacadeTwitter(null, $userId);

        $faker = Faker::create();
        $oauth_token = $faker->randomNumber();
        $oauth_verifier = $faker->randomNumber();
        $this->visit('auth/twitter/callback?oauth_token='.$oauth_token.'&oauth_verifier='.$oauth_verifier);

        $twitter_user = SocialUser::where('type', 'twitter')->where('social_id', $userId)->first();
        $user = $twitter_user->user;
        $this->assertNotEmpty($user, 'User was not created');
        $this->assertNotEmpty($user->twitter_user(), 'User was not linked to a Twitter SocialUser');

    }
    
    public function test_login_as_an_existing_twitter_user(){
        $faker = Faker::create();
        $userId = $faker->randomNumber(5);
        
        $this->mockSocialiteFacadeTwitter(null, $userId);
        $oauth_token = $faker->randomNumber();
        $oauth_verifier = $faker->randomNumber();

        $cb = $this->visit('auth/twitter/callback?oauth_token='.$oauth_token.'&oauth_verifier='.$oauth_verifier);
        $this->assertEquals('Login Redirect', $cb->crawler->filterXPath('//html/head/title')->text());
        $this->visit('user/current');
        
        $user1 = json_decode($this->response->getContent());
        $this->visit('logout');
        
        $this->visit('auth/twitter/callback?oauth_token='.$oauth_token.'&oauth_verifier='.$oauth_verifier);
        $this->assertEquals('Login Redirect', $cb->crawler->filterXPath('//html/head/title')->text());
        $this->visit('user/current');
        
        $user2 = json_decode($this->response->getContent());
        $this->visit('logout');
        
        $this->assertEquals($user1->id, $user2->id);
    }
    
    public function test_link_an_existing_user_to_twitter()
    {
        $faker = Faker::create();
        $email = $faker->email;
        $this->mockSocialiteFacadeTwitter($email);
        
        $user = new User;
        $user->email = $email;
        $user->save();
        $oauth_token = $faker->randomNumber();
        $oauth_verifier = $faker->randomNumber();
        $this->actingAs($user)
            ->visit('auth/twitter/callback?oauth_token='.$oauth_token.'&oauth_verifier='.$oauth_verifier);
            
        $this->assertNotEmpty($user->twitter_user(), 'User was not linked to a Twitter SocialUser');
    }
    
    public function test_link_an_existing_user_to_an_existing_twitter_user()
    {
        $faker = Faker::create();
        $email = $faker->email;
        $this->mockSocialiteFacadeTwitter($email);
        
        $user = new User;
        $user->email = $email;
        $user->save();
        $oauth_token = $faker->randomNumber();
        $oauth_verifier = $faker->randomNumber();
        $this->actingAs($user)
            ->visit('auth/twitter/callback?oauth_token='.$oauth_token.'&oauth_verifier='.$oauth_verifier);
            
        $this->assertNotEmpty($user->twitter_user(), 'User was not linked to a Twitter SocialUser');
        
        $this->actingAs($user)
            ->visit('auth/twitter/unlink')
            ->visit('auth/twitter/callback?oauth_token='.$oauth_token.'&oauth_verifier='.$oauth_verifier);
            
        $this->assertNotEmpty($user->twitter_user(), 'User was not linked to a Twitter SocialUser');
    }
}
