<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use App\User;

class UserTest extends TestCase
{
    use DatabaseTransactions;
    
    protected $free_user;
    
    public function setUp(){
        parent::setUp();
        $this->free_user = factory(\App\User::class)->create(['verified' => '1']);
    }
    
    public function test_title(){
        $ret = $this->visit('/users/'.$this->free_user->slug);

        $content = $ret->response->getContent();
        $res = preg_match("/<title>(.*)<\/title>/siU", $content, $title_matches);
        $title = preg_replace('/\s+/', ' ', $title_matches[1]);
        $title = trim($title);
        $this->assertContains($this->free_user->name, $title);
    }
}
