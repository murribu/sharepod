<?php namespace App\Http\Controllers;

use Auth;
use DB;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Exception;

use App\Connection;
use App\Episode;
use App\Hitcount;
use App\Show;
use App\User;

class UsersController extends Controller
{
    public function getMe(){
        return redirect('/users/' . Auth::user()->slug);
    }
    
    public function getUser($slug){
        $user = User::where('slug', $slug)->first();
        $activelink = Auth::user() && Auth::user()->slug == $slug ? 'me' : '';
        return view('user', compact('user', 'activelink'));
    }
    
    public function apiGetUser($slug){
        $u = User::where('slug', $slug)
            ->select('id', 'slug', 'name', 'email', 'facebook_user_id')
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
            $accepted_db = Connection::where('user_id', $user->id)->where('status', DB::raw("'approved'"))->get();
            $pending_db = Connection::where('user_id', $user->id)
                ->where(function($query){
                    $query->where('status', DB::raw("'viewed'"));
                    $query->orWhereNull('status');
                })->get();
            $accepted = [];
            foreach($accepted_db as $a){
                $accepted[] = [
                    'user_name' => $a->recommender->name,
                    'user_slug' => $a->recommender->slug
                ];
            }
            $pending = [];
            foreach($pending_db as $a){
                $pending[] = [
                    'user_name' => $a->recommender->name,
                    'user_slug' => $a->recommender->slug
                ];
            }
                
            return compact('accepted', 'pending');
        }
    }
    
    public function apiGetUserRecommendationsAccepted($slug){
        $user = User::where('slug', $slug)->first();
        $episodes = $user->info_for_feed()['episodes'];
        foreach($episodes as $e){
            $e = $e->prepare();
        }
        return $episodes;
    }
    
    public function apiGetUserArchivedEpisodes(){
        $user = Auth::user();
        if (!$user){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $user->archived_episodes();
    }
}