<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Podcatcher extends Model {
    use HasSlug;
    
    public $table = 'podcatchers';
    
    public function platforms(){
        return $this->hasMany('App\PodcatcherPlatform');
    }
    
    public function platforms_joined(){
        // dd($this->platforms()->pluck('platform')->toArray());
        return implode(", ", $this->platforms()->pluck('platform')->toArray());
    }
}