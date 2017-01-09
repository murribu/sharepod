<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Laravel\Socialite\Contracts\Factory as Socialite;

use App\Episode;
use App\Show;
use App\SocialUser;
use App\User;

class ShowTest extends TestCase
{
    use DatabaseTransactions;
    use MockSocialite;
    
}