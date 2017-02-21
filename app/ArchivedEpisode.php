<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveEpisode extends Model {
    use HasSlug;
    
    public $table = 'archived_episodes';
    
    public function users(){
        $self = $this;
        return User::whereIn('id', function($query) use ($self){
                $query->select('user_id')
                    ->from('archived_episode_requests')
                    ->where('archived_episode_id', $self->id)
                    ->where('status', 'success');
            })->get();
    }
    
    public function episode(){
        return $this->belongsTo('App\Episode');
    }
}