<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model {
    
    use HasSlug;
    
    public $table = 'episodes';
    
	public function img_url_default(){
		if ($this->img_url){
			return $this->img_url;
		}else{
			return $this->podcast->img_url;
		}
	}
}