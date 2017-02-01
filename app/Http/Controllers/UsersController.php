<?php namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Connection;
use App\Episode;
use App\Show;
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
    
    public function apiGetUserEpisodesLiked($slug){
        $user = User::where('slug', $slug)->first();
        if ($user){
            $episodes = Episode::leftJoin('shows', 'shows.id', '=', 'episodes.show_id')
                ->join('likes', function($join){
                    $join->on('likes.type', '=', DB::raw("'episode'"));
                    $join->on('fk', '=', 'episodes.id');
                })
            ->where('user_id', $user->id)
            ->select('likes.created_at as likeddate', 'episodes.name', 'episodes.slug', 'episodes.description', 'episodes.pubdate', 'episodes.img_url', 'shows.name as show_name', 'shows.slug as show_slug')
            ->orderBy('likes.created_at', 'desc')
            ->get();
            foreach($episodes as $e){
                $e = $e->prepare();
            }
            return $episodes;
        }
    }
    
    public function apiGetUserShowsLiked($slug){
        $user = User::where('slug', $slug)->first();
        if ($user){
            $shows = Show::join('likes', function($join){
                    $join->on('likes.type', '=', DB::raw("'show'"));
                    $join->on('fk', '=', 'shows.id');
                })
            ->where('user_id', $user->id)
            ->select('likes.created_at as likeddate', 'shows.name', 'shows.slug', 'shows.img_url', 'shows.description')
            ->orderBy('likes.created_at', 'desc')
            ->get();
            foreach($shows as $s){
                $s = $s->prepare();
            }
            return $shows;
        }
    }
    
    public function apiGetUserPlaylists($slug){
        $user = User::where('slug', $slug)->first();
        if ($user){
            $ret = [];
            $playlists = $user->playlists;
            
            foreach($playlists as $p){
                $ret[] = [
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'description' => $p->description,
                ];
            }
            
            return $ret;
        }
    }
    
    public function apiGetUserConnections($slug){
        $user = User::where('slug', $slug)->first();
        if ($user){
            $accepted = Connection::where('user_id', $user->id)->where('status', DB::raw("'approved'"))->get();
            $pending = Connection::where('user_id', $user->id)
                ->where(function($query){
                    $query->where('status', DB::raw("'viewed'"));
                    $query->orWhereNull('status');
                })->get();
            return compact('accepted', 'pending');
        }
    }
}