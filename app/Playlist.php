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
        $self = $this;
        return Episode::whereIn('id', function($query) use ($self){
            $query->select('episode_id')
                ->from('playlist_episodes')
                ->where('playlist_id', $self->id);
        })->get();
    }
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function info_for_feed(){
        $ret = [];
        $ret['episodes'] = Episode::join('playlist_episodes', 'playlist_episodes.episode_id', '=', 'episodes.id')
            ->selectRaw('episodes.*')
            ->orderBy('ordering')
            ->orderBy('id')
            ->get();
        $ret['url'] = env('APP_URL')."/playlists/".$this->slug."/feed";
        $ret['name'] = $this->name;
        
        return $ret;
    }
}