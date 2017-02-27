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
use App\Playlist;
use App\PlaylistEpisode;
use App\Episode;
use App\Notification;
use App\User;

class PlaylistTest extends TestCase
{
    use DatabaseTransactions;
    use InteractsWithPaymentProviders;
    
    protected $free_user;
    protected $basic_user;
    protected $playlist_info;

    public function setUp(){
        parent::setUp();
        $this->free_user = factory(\App\User::class)->create(['verified' => '1']);
        $this->basic_user = factory(\App\User::class)->create(['verified' => '1']);

        $this->actingAs($this->basic_user)
                ->json('POST', '/settings/subscription', [
                    'stripe_token' => $this->getStripeToken(),
                    'plan' => env('PLAN_BASIC_NAME'),
                ]);
                
        $faker = Faker::create();
        $this->playlist_info = [
            'name'          => $faker->name,
            'description'   => $faker->sentence
        ];
    }
    
    public function test_create_a_playlist(){
        
        $this->actingAs($this->free_user)
            ->visit('/playlists/new')
            ->see('New Playlist')
            ->type($this->playlist_info['name'], 'name')
            ->type($this->playlist_info['description'], 'description')
            ->press("Save");
            
        $playlist = Playlist::where('user_id', $this->free_user->id)->first();
        $this->assertNotNull($playlist);
        
        $this->visit('/playlists/'.$playlist->slug)
            ->see($this->playlist_info['name'])
            ->see($this->playlist_info['description']);
    }
    
    public function test_add_an_episode_to_a_playlist(){
        $p = factory(\App\Playlist::class)->create(['user_id' => $this->free_user->id]);
        $ep = factory(\App\Episode::class)->create();
        
        $ret = $this->actingAs($this->free_user)
            ->post('/api/playlists/'.$p->slug.'/add_episode', ['slug' => $ep->slug]);

        $pe = PlaylistEpisode::where('playlist_id', $p->id)
            ->where('episode_id', $ep->id)
            ->first();
            
        $this->assertNotNull($pe);
        
        $this->visit('/playlists/'.$p->slug)
            ->see($pe->episode->name);
    }
    
}