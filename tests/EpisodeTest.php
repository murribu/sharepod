<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use App\Episode;
use App\Show;
use App\SocialUser;
use App\User;

class EpisodeTest extends TestCase
{
    use DatabaseTransactions;
    
    protected $free_user;
    protected $ep;
    
    public function setUp(){
        parent::setUp();
        $this->free_user = factory(\App\User::class)->create(['verified' => '1']);
        
        $this->ep = factory(\App\Episode::class)->create();
    }
    
    public function test_like_an_episode(){
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
    
    public function test_title(){
        $ret = $this->visit('/episodes/'.$this->ep->slug);

        $content = $ret->response->getContent();
        $res = preg_match("/<title>(.*)<\/title>/siU", $content, $title_matches);
        $title = preg_replace('/\s+/', ' ', $title_matches[1]);
        $title = trim($title);
        $this->assertContains($this->ep->name, $title);
    }
}
