<?php
namespace Tests;

use Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Tests\Traits\MockSocialite;
use Laravel\Socialite\Contracts\Factory as Socialite;
use MailThief\Testing\InteractsWithMail;

use App\Connection;
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
        
        $episode1 = factory(\App\Episode::class)->create();
        $episode2 = factory(\App\Episode::class)->create();

        // if the receiving user doesn't already exist
        
            $this->actingAs($user1)
                ->post('/recommend', ['slug' => $episode1->slug, 'email_address' => $email2])
                ->seeJson(['success' => '1'], 'Error in sending an episode');
                
            $this->seeMessageFor($email2);
            $this->assertTrue($this->lastMessage()->contains('sent you a podcast episode'));
            
            // Check that a user was created
            $user2 = User::where('email', $email2)->first();
            $this->assertNotNull($user2);
            $this->assertEquals($user2->verified, 0);

            $recommendation1 = Recommendation::where('recommender_id', $user1->id)
                ->where('recommendee_id', $user2->id)
                ->where('episode_id', $episode1->id)
                ->orderBy('id', 'desc')
                ->first();
            
            $this->assertNotNull($recommendation1);
            $this->assertEquals($recommendation1->autoaction, 0);
            $this->assertNull($recommendation1->action);
            
            Auth::logout();
            
            $response = $this->visit('/recommendations/'.$recommendation1->slug); //this should login $user2
            $this->json('GET', '/api/recommendations/'.$recommendation1->slug)
                ->see($user1->name)
                ->see($episode1->name);
            
            $this->assertEquals(Auth::user()->id, $user2->id);
            
            $user2 = User::where('email', $email2)->first();
            $this->assertEquals($user2->verified, 1);
        
            $this->json('GET', '/api/recommendations_pending')
                ->see($episode1->slug);
            
            $connection = Connection::where('user_id', $user2->id)
                ->where('recommender_id', $user1->id)
                ->first();
                
            $connections = $this->json('GET', '/api/connections')
                ->seeJson(['connection_id' => $connection->id]);
        
            $this->json('POST', '/api/connections/approve', ['connection_id' => $connection->id]);
            
            $connection = Connection::where('user_id', $user2->id)
                ->where('recommender_id', $user1->id)
                ->first();//reload it
                
            $this->assertEquals($connection->status, 'approved');
        
            $this->actingAs($user1)
                ->visit('api/recent_recommendees')
                ->see($user2->name);
            
            $this->post('/recommend', ['slug' => $episode2->slug, 'email_address' => $email2])
                ->seeJson(['success' => '1'], 'Error in sending an episode');
                
            $recommendation2 = Recommendation::where('recommender_id', $user1->id)
                ->where('recommendee_id', $user2->id)
                ->where('episode_id', $episode2->id)
                ->orderBy('id', 'desc')
                ->first();

            $this->assertNotNull($recommendation2);
            $this->assertEquals($recommendation2->autoaction, 1);
            $this->assertEquals($recommendation2->action, 'accepted');
            
        
    }
}