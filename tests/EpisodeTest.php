<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Tests\Traits\MockSocialite;
use Laravel\Socialite\Contracts\Factory as Socialite;

use App\Episode;
use App\Show;
use App\SocialUser;
use App\User;

class EpisodeTest extends TestCase
{
    use DatabaseTransactions;
    use MockSocialite;
    
    protected $free_user;
    protected $ep;
    
    public function setUp(){
        parent::setUp();
        $this->free_user = factory(\App\User::class)->create(['verified' => '1']);
        
        $this->ep = factory(\App\Episode::class)->create();
    }
    
    public function test_create_a_new_user_and_like_an_episode()
    {
        $this->actingAs($this->free_user)
            ->post('/api/episodes/like', ['slug' => $this->ep->slug])
            ->seeJson(['success' => '1'], 'Error in liking an episode')
            ->post('/api/episodes/unlike', ['slug' => $this->ep->slug])
            ->seeJson(['success' => '1'], 'Error in unliking an episode');
    }
    
    public function test_viewing_an_episode(){
        $this->visit('/api/episodes/'.$this->ep->slug)
            ->seeJson(['name' => $this->ep->name])
            ->seeJson(['slug' => $this->ep->slug])
            ->seeJson(['show_name' => $this->ep->show->name])
            ->seeJson(['show_slug' => $this->ep->show->slug])
            ;
    }
}
