<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use App\Episode;
use App\Show;
use App\SocialUser;
use App\User;

class ShowTest extends TestCase
{
    use DatabaseTransactions;
    
    protected $free_user;
    protected $ep;
    
    public function setUp(){
        parent::setUp();
        $this->free_user = factory(\App\User::class)->create(['verified' => '1']);
        
        $this->show = factory(\App\Show::class)->create();
    }
    
    public function test_like_a_show(){
        $this->actingAs($this->free_user)
            ->post('/api/shows/like', ['slug' => $this->show->slug])
            ->seeJson(['success' => '1'], 'Error in liking a show')
            ->post('/api/shows/unlike', ['slug' => $this->show->slug])
            ->seeJson(['success' => '1'], 'Error in unliking a show');
    }
    
    public function test_viewing_a_show(){
        $this->visit('/api/shows/'.$this->show->slug)
            ->seeJson(['name' => $this->show->name])
            ->seeJson(['slug' => $this->show->slug]);
    }
    
    public function test_title(){
        $ret = $this->visit('/shows/'.$this->show->slug);

        $content = $ret->response->getContent();
        $res = preg_match("/<title>(.*)<\/title>/siU", $content, $title_matches);
        $title = preg_replace('/\s+/', ' ', $title_matches[1]);
        $title = trim($title);
        $this->assertContains($this->show->name, $title);
    }
}
