<?php namespace App;
use Auth;
use DB;
use Mail;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model {
    
    use HasSlug;
    use HasLikes;
    use HasFeed;
    
    public $table = 'playlists';
    public static $like_type = 'playlist';
    protected static $slug_reserved_words = ['new', 'popular'];
    
    public function episodes(){
        $episodes = Episode::join('playlist_episodes', 'playlist_episodes.episode_id', '=', 'episodes.id')
            ->leftJoin('shows', 'shows.id', '=', 'episodes.show_id')
            ->where('playlist_episodes.playlist_id', $this->id)
            ->selectRaw('episodes.*, shows.name show_name, shows.slug show_slug')
            ->orderBy('ordering')
            ->orderBy('id')
            ->get();
            
        foreach ($episodes as $e){
            $e = $e->prepare();
        }
        
        return $episodes;
    }
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function info_for_feed(){
        $ret = [];
        $ret['episodes'] = $this->episodes();
        $ret['url'] = env('APP_URL')."/playlists/".$this->slug."/feed";
        $ret['name'] = $this->name;
        
        return $ret;
    }
}