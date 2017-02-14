<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveEpisode extends Model {
    public $table = 'archive_episodes';
    
    public function user(){
        $self = $this;
        return User::whereIn('id', function($query) use ($self){
                $query->select('user_id')
                    ->from('archived_episode_users')
                    ->where('archived_episode_id', $self->id);
            })->get();
    }
    
    public function episode(){
        return $this->belongsTo('App\Episode');
    }
}