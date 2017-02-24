<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;
use Laravel\Spark\Spark;
use Laravel\Spark\Contracts\Interactions\Subscribe;

use Tests\Traits\InteractsWithPaymentProviders;

use App\ArchivedEpisode;
use App\ArchivedEpisodeUser;
use App\Episode;
use App\Notification;
use App\User;

class ArchivedEpisodeTest extends TestCase
{
    use DatabaseTransactions;
    use InteractsWithPaymentProviders;
    
    protected $user1;
    protected $ep;
    
    public function setUp(){
        parent::setUp();
        $this->user1 = factory(\App\User::class)->create(['verified' => '1']);
        
        //I placed this episode, to have something to work with
        $this->ep = factory(\App\Episode::class)->create(['url' => 'https://www.podcast.dj/EffectivelyWildEpisode783.mp3']);
    }
    
    public function test_requesting_to_archive_an_episode(){
        $ret = $this->actingAs($this->user1)
            ->post('/api/episodes/archive', ['slug' => $this->ep->slug]);
            
        $ae = ArchivedEpisodeUser::where('user_id', $this->user1->id)->first();
        
        $this->assertNotNull($ae);
        $this->assertNotNull($ae->archived_episode);
    }
    
    public function test_requesting_to_archive_an_episode_that_has_already_been_archived(){
        $ae = factory(\App\ArchivedEpisode::class)->create(['result_slug' => 'ok']);
        $aeu = factory(\App\ArchivedEpisodeUser::class)->create(['archived_episode_id' => $ae->id]);
        
        $ret = $this->actingAs($this->user1)
            ->post('/api/episodes/archive', ['slug' => $ae->episode->slug]);
            
        $response = json_decode($ret->response->getContent());
        $this->assertEquals($response->success, '1');
        $this->assertEquals($response->header, 'Episode archived!');
    }
    
    public function test_requesting_to_archive_an_episode_that_has_failed_multiple_times_before(){
        $aes = factory(\App\ArchivedEpisode::class, 6)->create(['result_slug' => 'http-not-found', 'episode_id' => $this->ep->id, 'processed_at' => '2017-1-1']);
        
        $ret = $this->actingAs($this->user1)
            ->post('/api/episodes/archive', ['slug' => $this->ep->slug]);
            
        $response = json_decode($ret->response->getContent());
        
        $this->assertEquals($response->success, '0');
        
        // Run this so that there isn't a stray record for subsequent tests
        $ret = $this->artisan('archive_one_episode');
    }
    
    public function test_archiving_an_episode_with_no_plan(){
        
        $ret = $this->actingAs($this->user1)
            ->post('/api/episodes/archive', ['slug' => $this->ep->slug]);
            
        $response = json_decode($ret->response->getContent());
        $aeu = ArchivedEpisodeUser::where('user_id', $this->user1->id)->first();
        $ae = $ae->archived_episode;
        $this->assertEquals($aeu->active, 0);
        $this->assertEquals($ae->result_slug, 'dj-storage-limit-exceeded');
        $this->assertContains('limit', $response->message);
    }

    public function test_archiving_an_episode_with_a_valid_plan(){
        $this->actingAs($this->user1)
                ->json('POST', '/settings/subscription', [
                    'stripe_token' => $this->getStripeToken(),
                    'plan' => env('PLAN_BASIC_NAME'),
                ]);
                        
        $ae = factory(\App\ArchivedEpisode::class)->create(['episode_id' => $this->ep->id]);
        $aeu = factory(\App\ArchivedEpisodeUser::class)->create(['archived_episode_id' => $ae->id, 'user_id' => $this->user1->id]);
        
        $ret = $this->artisan('archive_one_episode');
        $ae = $ae->fresh();
        $this->assertEquals($ae->result_slug, 'ok');
        $notification = Notification::where('user_id', $aeu->user_id)->first();
        $this->assertContains('Success', $notification->body);
    }
    
    public function test_unarchiving_an_episode(){
        $this->actingAs($this->user1)
                ->json('POST', '/settings/subscription', [
                    'stripe_token' => $this->getStripeToken(),
                    'plan' => env('PLAN_BASIC_NAME'),
                ]);
        $ae = factory(\App\ArchivedEpisode::class)->create(['episode_id' => $this->ep->id]);
        $aeu = factory(\App\ArchivedEpisodeUser::class)->create(['archived_episode_id' => $ae->id, 'user_id' => $this->user1->id]);
                
        $ret = $this->actingAs($this->user1)
            ->post('/api/episodes/unarchive', ['slug' => $this->ep->slug]);
        $response = json_decode($ret->response->getContent());
            
        $this->assertEquals($response->success, '1');
        
        $self = $this;
        $ae = ArchivedEpisodeUser::whereIn('archived_episode_id', function($query) use ($self){
            $query->select('id')
                ->from('archived_episodes')
                ->where('episode_id', $self->ep->id);
        })
        ->where('user_id', $this->user1->id)
        ->first();
        
        $this->assertEquals($ae->active, 0);
    }
}