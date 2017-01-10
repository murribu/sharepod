<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Tests\Traits\MockSocialite;
use Laravel\Socialite\Contracts\Factory as Socialite;

use App\SocialUser;
use App\User;

class FacebookTest extends TestCase
{
    use DatabaseTransactions;
    use MockSocialite;
    
    public function test_create_a_new_user_from_facebook_login()
    {
        $faker = Faker::create();
        $userId = $faker->randomNumber(5);
        $email = $faker->email;
        
        $this->mockSocialiteFacadeFacebook($email, $userId);

        $code = $faker->randomNumber();
        $state = $faker->randomNumber();
        $this->visit('auth/facebook/callback?code='.$code.'&state='.$state);

        $facebook_user = SocialUser::where('type', 'facebook')->where('social_id', $userId)->first();

        $user = $facebook_user->user;
        $this->assertNotEmpty($user, 'User was not created');
        $this->assertNotEmpty($user->facebook_user(), 'User was not linked to a Facebook SocialUser');

    }
    
    public function test_login_as_an_existing_facebook_user_and_a_subsequent_user(){
        $faker = Faker::create();
        $userId = $faker->randomNumber(5);
        $email = $faker->email;
        
        $this->mockSocialiteFacadeFacebook($email, $userId);
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
        
        $userId = $faker->randomNumber(5);
        $email = $faker->email;
        
        $this->mockSocialiteFacadeFacebook($email, $userId);
        $code = $faker->randomNumber();
        $state = $faker->randomNumber();

        $cb = $this->visit('auth/facebook/callback?code='.$code.'&state='.$state);
        $this->assertEquals('Login Redirect', $cb->crawler->filterXPath('//html/head/title')->text());
        $this->visit('user/current');
        
        $user3 = json_decode($this->response->getContent());
        
        $this->assertNotEmpty($user3, 'A subsequent user was not created');
    }
    
    public function test_link_an_existing_user_to_facebook()
    {
        $faker = Faker::create();
        $email = $faker->email;
        $this->mockSocialiteFacadeFacebook($email);
        
        $user = new User;
        $user->email = $email;
        $user->save();
        
        $code = $faker->randomNumber();
        $state = $faker->randomNumber();
        $this->actingAs($user)
            ->visit('auth/facebook/callback?code='.$code.'&state='.$state);
            
        $this->assertNotEmpty($user->facebook_user(), 'User was not linked to a Facebook SocialUser');
    }
    
    public function test_link_an_existing_user_to_an_existing_facebook_user(){
        $faker = Faker::create();
        $email = $faker->email;
        $this->mockSocialiteFacadeFacebook($email);
        
        $user = new User;
        $user->email = $email;
        $user->save();
        
        $code = $faker->randomNumber();
        $state = $faker->randomNumber();
        $this->actingAs($user)
            ->visit('auth/facebook/callback?code='.$code.'&state='.$state);
            
        $this->assertNotEmpty($user->facebook_user(), 'User was not linked to a Facebook SocialUser');
        
        $this->actingAs($user)
            ->visit('auth/facebook/unlink')
            ->visit('auth/facebook/callback?code='.$code.'&state='.$state);
            
        $this->assertNotEmpty($user->facebook_user(), 'User was not linked to a Facebook SocialUser');
    }
}
