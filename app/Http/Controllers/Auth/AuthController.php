<?php
namespace App\Http\Controllers\Auth;

use Auth;
use Illuminate\Support\Facades\Input;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Socialite;

use App\SocialUser;
use App\User;

class AuthController extends Controller
{
    
    /**
     * Redirect the user to the Twitter authentication page.
     *
     * @return Response
     */
    public function redirectToTwitter()
    {
        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Obtain the user information from Twitter.
     *
     * @return Response
     */
    public function handleTwitterCallback()
    {   
    
        $driver = Socialite::driver('twitter');
        
        $twitter_user = Socialite::driver('twitter')->user();

        // dd($twitter_user);
        
        $user = Auth::user();
        
        if (!$user){
            $user = User::create_from_twitter($twitter_user);
        }
        
        $user->link_to_twitter($twitter_user, Input::all());
    }
}