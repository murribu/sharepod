<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model {
    
    use HasSlug;
    
    protected static $slug_length = 64;
    
    public $table = 'recommendations';
    
}