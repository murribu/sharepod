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
    
    public function redirectToTwitter()
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function handleTwitterCallback()
    {   
    
        $driver = Socialite::driver('twitter');
        
        $twitter_user = Socialite::driver('twitter')->user();
        
        $user = Auth::user();
        
        if (!$user){
            $user = User::first_or_create_from_twitter($twitter_user);
        }
        
        $user->link_to_twitter($twitter_user, Input::all());
        
        Auth::login($user);
        
        return view('vendor.spark.auth.killwindow');
    }
    
    public function getMe(){
        return Auth::user();
    }
}