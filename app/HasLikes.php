<?php
namespace App;

trait HasLikes{
    
    public function likeCount(){
        $query = $this->likesQuery();
        
        return $query->count();
    }
    
    public function likes($user_id = false){
        $query = $this->likesQuery($user_id);
        
        return $query->get();
    }
    
    public function likesQuery($user_id = false){
        $query = Like::where('type', self::$like_type)
            ->where('fk', $this->id);
        if ($user_id){
            $query = $query->where('user_id', $user_id)
                ->orderBy('ordering');
        }
        
        return $query;
    }
    public function like($user){
        $l = Like::firstOrCreate([
            'user_id'   => $user->id,
            'fk'        => $this->id,
            'type'      => self::$like_type
        ]);
        return $l;
    }
    
    public function unlike($user){
        $l = Like::where('user_id', $user->id)
            ->where('fk', $this->id)
            ->where('type', self::$like_type)
            ->first();
            
        if ($l){
            $l->delete();
        }
        
        $ret = $this;
        $ret->success = 1;
        return $ret;
    }
}