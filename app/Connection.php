<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model {
    public $table = 'connections';
    
    protected $fillable = ['user_id', 'recommender_id'];
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function recommender(){
        return $this->belongsTo('App\User', 'id', 'recommeder_id');
    }
}