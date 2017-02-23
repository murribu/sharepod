<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchivedEpisodeResult extends Model {
    use HasSlug;
    
    public $table = 'archived_episode_results';
    
}