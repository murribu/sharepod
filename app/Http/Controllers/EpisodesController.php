<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Episode;

class EpisodesController extends Controller
{
    public function apiLike(){
        $user = Auth::user();
        if ($user){
            $ep = Episode::where('slug', Input::get('slug'))->first();
            if ($ep->like($user)){
                return ['success' => 1, 'total_likes' => $ep->likeCount(), 'this_user_likes' => 1];
            }
        }
    }
    
    public function apiUnlike(){
        $user = Auth::user();
        if ($user){
            $ep = Episode::where('slug', Input::get('slug'))->first();
            if ($ep->unlike($user)){
                return ['success' => 1, 'total_likes' => $ep->likeCount(), 'this_user_likes' => 0];
            }
        }
    }
}