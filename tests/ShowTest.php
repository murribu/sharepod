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

class ShowTest extends TestCase
{
    use DatabaseTransactions;
    use MockSocialite;
    
    public function test_create_a_new_user_and_like_an_episode()
    {
        $faker = Faker::create();
        $show = new Show;
        $show->feed = $faker->url;
        $show->slug = Show::findSlug();
        $show->save();
        
        $episode = new Episode;
        $episode->slug = Episode::findSlug();
        $episode->show_id = $show->id;
        $episode->save();
        
        $userId = $faker->randomNumber(5);

        $this->mockSocialiteFacadeFacebook($faker->email, $userId);

        $code = $faker->randomNumber();
        $state = $faker->randomNumber();
        $this->visit('auth/facebook/callback?code='.$code.'&state='.$state);

        $facebook_user = SocialUser::where('type', 'facebook')->where('social_id', $userId)->first();

        $user = $facebook_user->user;
        $this->assertNotEmpty($user, 'User was not created');
        $this->assertNotEmpty($user->facebook_user(), 'User was not linked to a Facebook SocialUser');

        $this->actingAs($user)
            ->post('/api/shows/like', ['slug' => $show->slug])
            ->seeJson(['success' => '1'], 'Error in liking a show')
            ->post('/api/shows/unlike', ['slug' => $show->slug])
            ->seeJson(['success' => '1'], 'Error in unliking a show');
    }
}
