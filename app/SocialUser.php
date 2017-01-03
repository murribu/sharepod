<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialUser extends Model {
    
    use HasSlug;
    
    public $table = 'social_user';
    
}