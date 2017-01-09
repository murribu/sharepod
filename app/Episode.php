<?php namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model {
    
    use HasSlug;
    
    public $table = 'episodes';
    
    public function like($user){
        $l = Like::firstOrCreate([
            'user_id'   => $user->id,
            'fk'        => $this->id,
            'type'      => 'episode'
        ]);
        return $l;
    }
    
    public function unlike($user){
        $l = Like::where('user_id', $user->id)
            ->where('fk', $this->id)
            ->where('type', 'episode')
            ->first();
            
        if ($l){
            $l->delete();
        }
        
        $ret = $this;
        $ret->success = 1;
        return $ret;
    }
    
    public function likes(){
        return Like::where('type', 'episode')
            ->where('fk', $this->id)
            ->get();
    }
    
    public function likeCount(){
        return Like::where('type', 'episode')
            ->where('fk', $this->id)
            ->count();
    }
    
	public function img_url_default(){
		if ($this->img_url){
			return $this->img_url;
		}else{
			return $this->podcast->img_url;
		}
	}
}