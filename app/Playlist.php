<?php namespace App;
use Auth;
use DB;
use Mail;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model {
    
    use HasSlug;
    use HasLikes;
    
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
}