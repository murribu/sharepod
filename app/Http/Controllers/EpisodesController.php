<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Episode;

class EpisodesController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified');
    }
    
    public function send(){
        $user = Auth::user();
        $ep = Episode::where('slug', Input::get('slug'))->first();
        if (Input::has('email_address')){
            return $ep->send_via_email(Input::all());
        }else if (Input::has('twitter_handle')){
            return $ep->send_via_twitter(Input::all());
        }
    }
    
    public function apiLike(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        if ($ep->like(Auth::user())){
            return ['success' => 1, 'total_likes' => $ep->likeCount(), 'this_user_likes' => 1];
        }
    }
    
    public function apiUnlike(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        if ($ep->unlike(Auth::user())){
            return ['success' => 1, 'total_likes' => $ep->likeCount(), 'this_user_likes' => 0];
        }
    }
}