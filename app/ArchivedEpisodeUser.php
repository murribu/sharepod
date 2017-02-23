<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchivedEpisodeUser extends Model {
    public $table = 'archived_episode_users';
    
    protected $fillable = ['archived_episode_id', 'user_id'];
    
    public function archived_episode(){
        return $this->belongsTo('App\ArchivedEpisode');
    }
}