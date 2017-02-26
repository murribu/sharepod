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

    public function setUp(){
        parent::setUp();
        $this->free_user = factory(\App\User::class)->create(['verified' => '1']);
        $this->basic_user = factory(\App\User::class)->create(['verified' => '1']);

        $this->actingAs($this->basic_user)
                ->json('POST', '/settings/subscription', [
                    'stripe_token' => $this->getStripeToken(),
                    'plan' => env('PLAN_BASIC_NAME'),
                ]);
    }
    
    

}