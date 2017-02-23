<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchivedEpisode extends Model {
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
    
    public function create_archived_episode_user($user){
        $aeu = ArchivedEpisodeUser::firstOrCreate(['archived_episode_id' => $this->id, 'user_id' => $user->id]);
        $aeu->active = 1;
        $aeu->save();
    }
    
    public function episode(){
        return $this->belongsTo('App\Episode');
    }
}