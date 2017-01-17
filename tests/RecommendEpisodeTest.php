<?php
namespace Tests;

use Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Tests\Traits\MockSocialite;
use Laravel\Socialite\Contracts\Factory as Socialite;
use MailThief\Testing\InteractsWithMail;

use App\Episode;
use App\Recommendation;
use App\Show;
use App\SocialUser;
use App\User;

class RecommendEpisodeTest extends TestCase
{
    use MockSocialite;
    use InteractsWithMail;
    use DatabaseTransactions;
    
    public function test_recommend_an_episode_via_email(){
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
        
        $episode = factory(\App\Episode::class)->create();

        // if the receiving user doesn't already exist
        
            $this->actingAs($user1)
                ->post('/recommend', ['slug' => $episode->slug, 'email_address' => $email2])
                ->seeJson(['success' => '1'], 'Error in sending an episode');
                
            $this->seeMessageFor($email2);
            $this->assertTrue($this->lastMessage()->contains('sent you a podcast episode'));
            
            // Check that a user was created
            $user2 = User::where('email', $email2)->first();
            $this->assertNotNull($user2);
            $this->assertEquals($user2->verified, 0);

            $recommendation = Recommendation::where('recommender_id', $user1->id)
                ->where('recommendee_id', $user2->id)
                ->orderBy('id', 'desc')
                ->first();
            
            $this->assertNotNull($recommendation);
            
            Auth::logout();
            
            $this->visit('/recommendation/'.$recommendation->slug);
            $this->visit('/api/recommendation/'.$recommendation->slug)
                ->see($user1->name)
                ->see($episode->name);
            
            $this->assertEquals(Auth::user()->id, $user2->id);
            
            $user2 = User::where('email', $email2)->first();
            $this->assertEquals($user2->verified, 1);
            
            
        // if the receiving user already exists
            
        Auth::logout();
        
        $this->actingAs($user1)
            ->visit('api/recent_recommendees')
            ->see($user2->name);
    }
}