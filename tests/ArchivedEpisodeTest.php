<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;
use Laravel\Spark\Spark;
use Laravel\Spark\Contracts\Interactions\Subscribe;
use Carbon\Carbon;

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
    
    protected $free_user;
    protected $basic_user;
    protected $ep;
    
    public function setUp(){
        parent::setUp();
        ArchivedEpisode::whereNull('processed_at')->update(['processed_at' => Carbon::now()]);
        $this->free_user = factory(\App\User::class)->create(['verified' => '1']);
        $this->basic_user = factory(\App\User::class)->create(['verified' => '1']);

        $this->actingAs($this->basic_user)
                ->json('POST', '/settings/subscription', [
                    'stripe_token' => $this->getStripeToken(),
                    'plan' => env('PLAN_BASIC_NAME'),
                ]);
        
        //I placed this episode, to have something to work with
        $this->ep = factory(\App\Episode::class)->create(['url' => 'https://www.podcast.dj/EffectivelyWildEpisode783.mp3']);
    }

    public function test_archiving_an_episode_with_a_valid_plan(){
        $ae = factory(\App\ArchivedEpisode::class)->create(['episode_id' => $this->ep->id]);
        $aeu = factory(\App\ArchivedEpisodeUser::class)->create(['archived_episode_id' => $ae->id, 'user_id' => $this->basic_user->id]);
        
        $ret = $this->artisan('archive_one_episode');
        $ae = $ae->fresh();
        $this->assertEquals($ae->result_slug, 'ok');
        $notification = Notification::where('user_id', $aeu->user_id)->first();
        $this->assertContains('Success', $notification->body);
    }
    
    public function test_requesting_to_archive_an_episode_with_a_valid_plan(){
        $ret = $this->actingAs($this->basic_user)
            ->post('/api/episodes/archive', ['slug' => $this->ep->slug]);
        $response = json_decode($ret->response->getContent());
        //dd($response);
        $ae = ArchivedEpisodeUser::where('user_id', $this->basic_user->id)->first();
        
        $this->assertNotNull($ae);
        $this->assertNotNull($ae->archived_episode);
    }
    
    public function test_requesting_to_archive_an_episode_that_has_already_been_archived_with_no_plan(){
        $ae = factory(\App\ArchivedEpisode::class)->create(['result_slug' => 'ok']);
        $aeu = factory(\App\ArchivedEpisodeUser::class)->create(['archived_episode_id' => $ae->id]);
        
        $ret = $this->actingAs($this->free_user)
            ->post('/api/episodes/archive', ['slug' => $ae->episode->slug]);
            
        $response = json_decode($ret->response->getContent());
        $this->assertEquals($response->success, '0');
    }
    
    public function test_requesting_to_archive_an_episode_that_has_already_been_archived(){
        $ae = factory(\App\ArchivedEpisode::class)->create(['result_slug' => 'ok']);
        $aeu = factory(\App\ArchivedEpisodeUser::class)->create(['archived_episode_id' => $ae->id]);
        
        $ret = $this->actingAs($this->basic_user)
            ->post('/api/episodes/archive', ['slug' => $ae->episode->slug]);
            
        $response = json_decode($ret->response->getContent());
        $this->assertEquals($response->success, '1');
        $this->assertEquals($response->header, 'Episode archived!');
    }
    
    public function test_requesting_to_archive_an_episode_that_has_failed_multiple_times_before(){
        $aes = factory(\App\ArchivedEpisode::class, 6)->create(['result_slug' => 'http-not-found', 'episode_id' => $this->ep->id, 'processed_at' => '2017-1-1']);
        
        $ret = $this->actingAs($this->basic_user)
            ->post('/api/episodes/archive', ['slug' => $this->ep->slug]);
            
        $response = json_decode($ret->response->getContent());
        
        $this->assertEquals($response->success, '0');
        
        $count = ArchivedEpisode::whereNull('processed_at')->count();
        
        $this->assertEquals($count, 0);
        
    }
    
    public function test_archiving_an_episode_with_no_plan(){
        $ret = $this->actingAs($this->free_user)
            ->post('/api/episodes/archive', ['slug' => $this->ep->slug]);
            
        $response = json_decode($ret->response->getContent());
        $aeu = ArchivedEpisodeUser::where('user_id', $this->free_user->id)->orderBy('id', 'desc')->first();
        $this->assertNull($aeu);
        $this->assertContains('does not allow', $response->message);

        $count = ArchivedEpisode::whereNull('processed_at')->count();
        
        $this->assertEquals($count, 0);
    }
    
    public function test_unarchiving_an_episode(){
        $ae = factory(\App\ArchivedEpisode::class)->create(['episode_id' => $this->ep->id]);
        $aeu = factory(\App\ArchivedEpisodeUser::class)->create(['archived_episode_id' => $ae->id, 'user_id' => $this->basic_user->id]);
                
        $ret = $this->actingAs($this->basic_user)
            ->post('/api/episodes/unarchive', ['slug' => $this->ep->slug]);
        $response = json_decode($ret->response->getContent());
            
        $this->assertEquals($response->success, '1');
        
        $self = $this;
        $ae = ArchivedEpisodeUser::whereIn('archived_episode_id', function($query) use ($self){
            $query->select('id')
                ->from('archived_episodes')
                ->where('episode_id', $self->ep->id);
        })
        ->where('user_id', $this->basic_user->id)
        ->first();
        
        $this->assertEquals($ae->active, 0);
    }
}