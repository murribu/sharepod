<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class PodcatcherPlatform extends Model {
    public $table = 'podcatcher_platforms';
    
    protected $fillable = ['podcatcher_id', 'platform'];
    
    public function podcatcher(){
        return $this->belongsTo('App\Podcatcher');
    }
}