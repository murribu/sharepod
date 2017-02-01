<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\User;

class UsersController extends Controller
{
    public function getUser($slug){
        return view('user', ['activelink' => 'connections']);
    }
    
    public function apiGetUser($slug){
        $u = User::where('slug', $slug)
            ->select('id', 'slug', 'name', 'email')
            ->first();
            
        $u->photo_url = $u->getPhotoUrlAttribute($u->photo_url);
        
        return $u;
    }
    
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