<?php
namespace App;

trait HasLikes{
    
    public function likesCount(){
        $query = $this->likesQuery();
        
        return $query->count();
    }
    
    public function likes($user_id = false){
        $query = $this->likesQuery($user_id);
        
        return $query->get();
    }
    
    public function likesQuery($user_id = false){
        $query = Like::where('type', strtolower(get_class($this)))
            ->where('fk', $this->id);
        if ($user_id){
            $query = $query->where('user_id', $user_id)
                ->orderBy('ordering');
        }
        
        return $query;
    }
}