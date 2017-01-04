<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialUser extends Model {
    
    use HasSlug;
    
    public $table = 'social_users';
    
    public function user(){
        if ($this->type == 'twitter'){
            return $this->belongsTo('App\User', 'twitter_user_id');
        }else if($this->type == 'facebook'){
            return $this->belongsTo('App\User', 'facebook_user_id');
        }
    }
}