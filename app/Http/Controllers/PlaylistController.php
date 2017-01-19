<?php namespace App\Http\Controllers;

use Auth;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Hitcount;
use App\User;

class PlaylistController extends Controller
{
    public function getFeed($slug){
        $user = User::where('slug', $slug)->first();
        if ($user){
            $hitcount = new Hitcount;
            $hitcount->request = 'user_feed';
            $hitcount->user_id = $user ? $user->id : null;
            $hitcount->ip = \Request::getClientIp();
            $hitcount->fk = $user->id;
            $hitcount->save();
            
            return Response::make($user->feed(), 200)->header('Content-Type', 'application/xml');
        }
    }
}