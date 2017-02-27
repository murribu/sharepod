<?php namespace App\Http\Controllers;

use Auth;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Episode;
use App\Hitcount;
use App\Playlist;
use App\PlaylistEpisode;
use App\User;

class PlaylistController extends Controller {
    public function getPlaylists(){
        return view('playlists', ['activelink' => 'playlists']);
    }
    
    public function getPlaylist($slug){
        $playlist = Playlist::where('slug', $slug)->first();
        $activelink = 'playlists';
        return view('playlist', compact('playlist', 'activelink'));
    }
    
    public function getEdit($slug = null){
        $user = Auth::user();
        $playlist = Playlist::where('slug', $slug)->first();
        if ($playlist){
            if (!$user || $playlist->user_id != $user->id){
                return redirect('/');
            }
        }
        return view('playlist_edit', compact('playlist'));
    }
    
    public function postEdit($slug = null){
        $playlist = Playlist::where('slug', $slug)->first();
        $user = Auth::user();
        if ($playlist){
            if (!$user || $playlist->user_id != $user->id){
                return redirect('/');
            }
        }else{
            $permissions = $user->plan_permissions();
            if (!$permissions['can_add_a_playlist']){
                $msg = 'You have reached the maximum number of Playlists for your Subscription Plan. <a href="/settings#/subscription">Click here</a> to change your Plan.';
                $statusClass = 'alert-danger';
                return redirect('/playlists')->with(compact('msg', 'statusClass'));
            }
            $playlist = new Playlist;
            $playlist->slug = Playlist::findSlug(Input::get('name'));
            $playlist->user_id = $user->id;
        }
        $playlist->name = Input::get('name');
        $playlist->description = Input::get('description');
        $playlist->save();
        
        return redirect('/playlists/'.$playlist->slug);
    }
    
    public function apiGetPlaylist($slug){
        $ret = [];
        $playlist = Playlist::where('slug', $slug)->first();
        if ($playlist){
            $ret['name'] = $playlist->name;
            $ret['slug'] = $playlist->slug;
            $ret['description'] = $playlist->description;
            $ret['user_name'] = $playlist->user->name;
            $ret['user_slug'] = $playlist->user->slug;
            $episodes = $playlist->episodes();
            foreach($episodes as $e){
                $e = $e->prepare();
            }
            $ret['episodes'] = $episodes;
        }
        
        return $ret;
    }
    
    public function apiGetPlaylists(){
        return Playlist::where('user_id', Auth::user()->id)
            ->get();
    }
    
    public function apiGetPopularPlaylists($limit = 5){
        return Playlist::leftJoin('hitcounts', function($join){
            $join->on('hitcounts.request', '=', DB::raw("'playlist_feed'"));
            $join->on('hitcounts.fk', '=', 'playlists.id');
        })
        ->leftJoin('users', 'users.id', '=', 'playlists.user_id')
        ->selectRaw('count(hitcounts.id) c, playlists.slug, playlists.name, users.slug user_slug, users.name user_name')
        ->groupBy('playlists.slug')
        ->groupBy('playlists.name')
        ->groupBy('users.slug')
        ->groupBy('users.name')
        ->orderBy('c', 'desc')
        ->limit($limit)
        ->get();
    }
    
    public function apiPostAddEpisode($slug){
        $playlist = Playlist::where('slug', $slug)->first();
        $episode = Episode::where('slug', Input::get('slug'))->first();
        $user = Auth::user();
        if ($playlist && $episode && $user && $playlist->user_id == $user->id){
            $p = PlaylistEpisode::firstOrCreate(['episode_id' => $episode->id, 'playlist_id' => $playlist->id]);
            $pe = PlaylistEpisode::join('playlists', 'playlists.id', '=', 'playlist_episodes.playlist_id')
                ->where('episode_id', $episode->id)
                ->selectRaw('count(distinct playlist_episodes.id) c')
                ->first();
            if ($p){
                return ['success' => 1, 'total_playlists' => $pe->c];
            }else{
                return response()->json('There was a problem adding this episode to this playlist', 500);
            }
        }else{
            return response()->json('Playlist or Episode not found, or you don\'t have access to it', 400);
        }
        
    }
    
    public function getFeed($slug){
        $playlist = Playlist::where('slug', $slug)->first();
        if ($playlist){
            $hitcount = new Hitcount;
            $hitcount->request = 'user_feed';
            $hitcount->user_id = Auth::user() ? Auth::user()->id : null;
            $hitcount->ip = \Request::getClientIp();
            $hitcount->fk = $playlist->id;
            $hitcount->save();
            
            return Response::make($playlist->feed(), 200)->header('Content-Type', 'application/xml');
        }
    }
    
    public function apiPostMoveUp($slug){
        $playlist = Playlist::where('slug', $slug)->first();
        if ($playlist->user_id == Auth::user()->id){
            DB::update("update playlist_episodes set ordering = coalesce(ordering, id) where playlist_id = ?", [$playlist->id]);
            $pe = PlaylistEpisode::where('playlist_id', $playlist->id)
                ->where('episode_id', function($query){
                    $query->select('id')
                        ->from('episodes')
                        ->where('slug', Input::get('slug'));
                })->first();
            $swap = PlaylistEpisode::where('playlist_id', $playlist->id)
                ->where('ordering', '<', $pe->ordering)
                ->orderBy('ordering', 'desc')
                ->orderBy('id', 'desc')
                ->first();
            $temp = $pe->ordering;
            $pe->ordering = $swap->ordering;
            $swap->ordering = $temp;
            $pe->save();
            $swap->save();
            
            return $playlist->episodes();
        }else{
            return response()->json('This is not your playlist', 403);
        }
    }
    
    public function apiPostMoveDown($slug){
        $playlist = Playlist::where('slug', $slug)->first();
        if ($playlist->user_id == Auth::user()->id){
            DB::update("update playlist_episodes set ordering = coalesce(ordering, id) where playlist_id = ?", [$playlist->id]);
            $pe = PlaylistEpisode::where('playlist_id', $playlist->id)
                ->where('episode_id', function($query){
                    $query->select('id')
                        ->from('episodes')
                        ->where('slug', Input::get('slug'));
                })->first();
            $swap = PlaylistEpisode::where('playlist_id', $playlist->id)
                ->where('ordering', '>', $pe->ordering)
                ->orderBy('ordering')
                ->orderBy('id')
                ->first();
            $temp = $pe->ordering;
            $pe->ordering = $swap->ordering;
            $swap->ordering = $temp;
            $pe->save();
            $swap->save();
            
            return $playlist->episodes();
        }else{
            return response()->json('This is not your playlist', 403);
        }
    }
    
    public function apiPostMoveToTop($slug){
        $playlist = Playlist::where('slug', $slug)->first();
        if ($playlist->user_id == Auth::user()->id){
            DB::update("update playlist_episodes set ordering = coalesce(ordering, id) where playlist_id = ?", [$playlist->id]);
            $pe = PlaylistEpisode::where('playlist_id', $playlist->id)
                ->where('episode_id', function($query){
                    $query->select('id')
                        ->from('episodes')
                        ->where('slug', Input::get('slug'));
                })->first();
            DB::update("update playlist_episodes set ordering = ordering + 1 where playlist_id = ? and ordering < ?", [$playlist->id, $pe->ordering]);
            $pe->ordering = 1;
            $pe->save();
            
            return $playlist->episodes();
        }else{
            return response()->json('This is not your playlist', 403);
        }
    }
    
    public function apiPostMoveToBottom($slug){
        $playlist = Playlist::where('slug', $slug)->first();
        if ($playlist->user_id == Auth::user()->id){
            DB::update("update playlist_episodes set ordering = coalesce(ordering, id) where playlist_id = ?", [$playlist->id]);
            $pe = PlaylistEpisode::where('playlist_id', $playlist->id)
                ->where('episode_id', function($query){
                    $query->select('id')
                        ->from('episodes')
                        ->where('slug', Input::get('slug'));
                })->first();
            $max = PlaylistEpisode::where('playlist_id', $playlist->id)
                ->max('ordering');
            DB::update("update playlist_episodes set ordering = ordering - 1 where playlist_id = ? and ordering > ?", [$playlist->id, $pe->ordering]);
            $pe->ordering = $max;
            $pe->save();
            
            return $playlist->episodes();
        }else{
            return response()->json('This is not your playlist', 403);
        }
    }
    
    public function apiPostRemove($slug){
        $playlist = Playlist::where('slug', $slug)->first();
        if ($playlist->user_id == Auth::user()->id){
            $pe = PlaylistEpisode::where('playlist_id', $playlist->id)
                ->where('episode_id', function($query){
                    $query->select('id')
                        ->from('episodes')
                        ->where('slug', Input::get('slug'));
                })->delete();
            
            return $playlist->episodes();
        }else{
            return response()->json('This is not your playlist', 403);
        }
    }
}