<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveEpisodeUser extends Model {
    public $table = 'archived_episode_user';
    
    protected $fillable = ['archived_episode_id', 'user_id'];
}