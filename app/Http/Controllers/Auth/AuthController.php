<?php
namespace App\Http\Controllers\Auth;

use Auth;
use Illuminate\Support\Facades\Input;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Socialite;
use Jrean\UserVerification\Facades\UserVerification;
use Jrean\UserVerification\Traits\VerifiesUsers;

use App\SocialUser;
use App\User;

class AuthController extends Controller
{    
    use VerifiesUsers;
    
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {   
        $driver = Socialite::driver('facebook');
        
        $facebook_user = Socialite::driver('facebook')->user();
        
        $user = Auth::user();
        
        if (!$user){
            $user = User::first_or_create_from_facebook($facebook_user);
        }
        
        $user->link_to_facebook($facebook_user, Input::all());
        
        Auth::login($user);
        
        return view('vendor.spark.auth.killwindow');
    }
    
    public function unlinkFacebook(){
        $user = Auth::user();
        $user->facebook_user_id = null;
        $user->save();
        
        return $user;
    }
    
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
    
    public function unlinkTwitter(){
        $user = Auth::user();
        $user->twitter_user_id = null;
        $user->save();
        
        return $user;
    }
    
    public function sendVerificationEmail(){
        $user = Auth::user();
        
        UserVerification::generate($user);
        UserVerification::send($user, 'Verify your email for Shaarepod');
    }
}