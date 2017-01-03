<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Laravel\Socialite\Contracts\Factory as Socialite;

class TwitterTest extends TestCase
{
    // use DatabaseTransactions;
    
    // /**
     // * Mock the Socialite Factory, so we can hijack the OAuth Request.
     // * @param  string  $email
     // * @param  string  $token
     // * @param  int $id
     // * @return void
     // */
    // public function testTwitterLogin()
    // {
        // $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');         
        // $abstractUser->shouldReceive('getId') 
            // ->andReturn(1234567890)
            // ->shouldReceive('getEmail')
            // ->andReturn(str_random(10).'@test.com')
            // ->shouldReceive('getNickname')
            // ->andReturn('Pseudo')
            // ->shouldReceive('getName')
            // ->andReturn('Arlette Laguiller')
            // ->shouldReceive('getAvatar')
            // ->andReturn('https://en.gravatar.com/userimage');

        // $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        // $provider->shouldReceive('user')->andReturn($abstractUser);

        // Socialite::shouldReceive('driver')->with('twitter')->andReturn($provider);

        // $this->visit("/auth/twitter/callback")
            // ->seePageIs("/");
    // }
}
