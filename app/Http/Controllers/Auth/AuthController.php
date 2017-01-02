<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Socialite;

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
     * Obtain the user information from Tiwtter.
     *
     * @return Response
     */
    public function handleTwitterCallback()
    {
        $twitter_user = Socialite::driver('twitter')->user();

        $user = Auth::user();
        if (!$user){
            //create an account
            $user = new User;
            $user->name = $twitter_user->name;
            if ($twitter_user->email){
                $user->email = $twitter_user->email;
            }else{
                $user->email = '@'.$twitter_user->nickname;
            }
        }
    }
}