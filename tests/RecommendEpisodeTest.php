<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Tests\Traits\MockSocialite;
use Laravel\Socialite\Contracts\Factory as Socialite;
use MailThief\Testing\InteractsWithMail;

use App\Episode;
use App\Show;
use App\SocialUser;
use App\User;

class RecommendEpisodeTest extends TestCase
{
    use DatabaseTransactions;
    use MockSocialite;
    use InteractsWithMail;
    
    public function test_send_an_episode_via_email(){
        $faker = Faker::create();
        $code = $faker->randomNumber();
        $state = $faker->randomNumber();
        
        $userId1 = $faker->randomNumber(5);
        $email1 = $faker->email;
        $email2 = $faker->email;
        $this->mockSocialiteFacadeFacebook($email1, $userId1);

        $this->visit('auth/facebook/callback?code='.$code.'&state='.$state);
        
        $facebook_user = SocialUser::where('type', 'facebook')->where('social_id', $userId1)->first();

        $user1 = $facebook_user->user;
        
        $episode = factory(\App\Episode::class)->make();
        
        // if the receiving user doesn't already exist
        
            $this->post('/send', ['slug' => $episode->slug, 'email_address' => $email2])
                ->seeJson(['success' => '1'], 'Error in sending an episode');
                
            $this->seeMessageFor($email2);
            $this->assertTrue($this->lastMessage()->contains('sent you a podcast episode'));
            
            // Check that a user was created
            $user2 = User::where('email', $email2)->first();
            $this->assertNotNull($user2);
            $this->assertNotNull($user2->verification_token);
            $this->assertEquals($user2->verified, 0);
        
        // if the receiving user already exists
            
            
    }
}