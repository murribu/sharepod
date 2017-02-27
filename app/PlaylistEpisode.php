<?php namespace App;

use DB;

use Illuminate\Database\Eloquent\Model;

class PlaylistEpisode extends Model {
    
    public $table = 'playlist_episodes';
    
    protected $fillable = ['playlist_id', 'episode_id'];
    
    public function episode(){
        return $this->belongsTo('App\Episode');
    }
}