<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

use Laravel\Socialite\Contracts\Factory as Socialite;

use App\Episode;
use App\Show;
use App\SocialUser;
use App\User;

class EpisodeTest extends TestCase
{
    use DatabaseTransactions;
    
    public function tearDown(){
        Mockery::close();
    }
    
    public function mockSocialiteFacade($email = 'foo@bar.com', $id = null)
    {
        $faker = Faker::create();
        if (!$id){
            $id = $faker->randomNumber(5);
        }
        $name = $faker->name;
        $url = $faker->url;
        
        $socialiteUser = Mockery::mock(Laravel\Socialite\Two\User::class);
        $socialiteUser->token = $faker->randomNumber(5);
        $socialiteUser->id = $id;
        $socialiteUser->nickname = $faker->userName;
        $socialiteUser->name = $name;
        $socialiteUser->email = $email;
        $socialiteUser->avatar = $faker->imageUrl();
        $socialiteUser->avatar_original = $faker->imageUrl();
        $socialiteUser->profileUrl = $url;
        $socialiteUser->user = [
            'name' => $name,
            'email' => $email,
            'gender' => $faker->word,
            'verified' => true,
            'link' => $url,
            'id' => $id,
        ];

        $provider = Mockery::mock(Laravel\Socialite\Two\FacebookProvider::class);
        $provider->shouldReceive('user')
            ->andReturn($socialiteUser);

        $stub = Mockery::mock(Socialite::class);
        $stub->shouldReceive('driver')->with('facebook')
            ->andReturn($provider);

        // Replace Socialite Instance with our mock
        $this->app->instance(Socialite::class, $stub);
    }
    
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

        $this->mockSocialiteFacade($faker->email, $userId);

        $code = $faker->randomNumber();
        $state = $faker->randomNumber();
        $this->visit('auth/facebook/callback?code='.$code.'&state='.$state);

        $facebook_user = SocialUser::where('type', 'facebook')->where('social_id', $userId)->first();

        $user = $facebook_user->user;
        $this->assertNotEmpty($user, 'User was not created');
        $this->assertNotEmpty($user->facebook_user(), 'User was not linked to a Facebook SocialUser');

        $this->actingAs($user)
            ->post('/api/episodes/like', ['slug' => $episode->slug])
            ->seeJson(['success' => '1'], 'Error in liking an episode')
            ->post('/api/episodes/unlike', ['slug' => $episode->slug])
            ->seeJson(['success' => '1'], 'Error in unliking an episode');
    }
}
