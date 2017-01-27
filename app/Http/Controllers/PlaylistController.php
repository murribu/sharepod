<?php namespace App\Http\Controllers;

use Auth;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Hitcount;
use App\Playlist;
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
        $playlist = Playlist::where('slug', $slug)->first();
        if ($playlist){
            $user = Auth::user();
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
            $episodes = [];
            foreach($playlist->episodes() as $e){
                $episodes[] = [
                    'show_name' => $e->show->name,
                    'show_slug' => $e->show->slug,
                    'name' => $e->name,
                    'slug' => $e->slug,
                ];
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
}