<?php namespace App;
use Auth;
use DB;
use Mail;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model {
    
    use HasSlug;
    use HasLikes;
    
    public $table = 'episodes';
    public static $like_type = 'episode';
    
	public function img_url_default(){
		if ($this->img_url){
			return $this->img_url;
		}else{
			return $this->podcast->img_url;
		}
	}
}