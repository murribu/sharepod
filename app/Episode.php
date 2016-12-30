<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model {
    
    use HasSlug;
    
    public $table = 'episodes';
    
}