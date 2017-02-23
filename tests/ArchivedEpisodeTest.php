<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Tests\Traits\MockSocialite;
use Laravel\Socialite\Contracts\Factory as Socialite;

use App\ArchivedEpisode;
use App\ArchivedEpisodeUser;
use App\Episode;
use App\Show;
use App\SocialUser;
use App\User;

class ArchivedEpisodeTest extends TestCase
{
    use MockSocialite;
    use DatabaseTransactions;
    
    protected $user1;
    protected $ep;
    
    public function setUp(){
        parent::setUp();
        $this->user1 = factory(\App\User::class)->create(['verified' => '1']);
        
        //I placed this episode, to have something to work with
        $this->ep = factory(\App\Episode::class)->create(['url' => 'https://www.podcast.dj/EffectivelyWildEpisode783.mp3']);
    }
    
    public function test_requesting_to_archive_an_episode(){
        $this->actingAs($this->user1)
            ->post('/api/episodes/archive', ['slug' => $this->ep->slug]);
            
        $ae = ArchivedEpisodeUser::where('user_id', $this->user1->id)->first();
        
        $this->assertNotNull($ae);
        $this->assertNotNull($ae->archived_episode);
    }
    
    public function test_requesting_to_archive_an_episode_that_has_already_been_archived(){
        $ae = factory(\App\ArchivedEpisode::class)->create(['status_code' => '200']);
        $aeu = factory(\App\ArchivedEpisodeUser::class)->create(['archived_episode_id' => $ae->id]);
        
        $ret = $this->actingAs($this->user1)
            ->post('/api/episodes/archive', ['slug' => $ae->episode->slug]);
            
        $response = json_decode($ret->response->getContent());
        
        $this->assertEquals($response->success, '1');
        $this->assertEquals($response->message, 'Episode archived!');
    }
}