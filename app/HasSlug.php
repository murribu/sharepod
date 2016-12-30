<?php
namespace App;

// ENHANCEMENT TODO:
// self::$reserved_words, like 'new', 'edit', etc

trait HasSlug{
    
    public static function findSlug($slug = false){
        $field = property_exists( new self, 'slug_field') ? self::$slug_field : 'slug';
        if ($slug){
            $slug = strtolower(preg_replace("/[^a-zA-Z\d]/", "-", $slug));
        }else{
            $slug = self::generateRandomString(32);
        }
        $exists = self::where($field, $slug)->first();
        if (!$exists && $slug != 'new'){
            return $slug;
        }
        $i = 0;
        while ($exists || $slug == 'new'){
            $exists = self::where($field, $slug."-".++$i)->first();
        }
        return $slug."-".$i;
    }
    
    public static function findSlugWithPrefix($prefix){
        $field = property_exists( new self, 'slug_field') ? self::$slug_field : 'slug';
        if (strlen($prefix) >= 30){
            throw new Exception("Prefix must be shorter than 30 characters");
        }
        $slug = $prefix . self::generateRandomString(32 - strlen($prefix));
        $exists = self::where($field, $slug)->first();
        if (!$exists && $slug != 'new'){
            return $slug;
        }
        $i = 0;
        while ($exists || $slug == 'new'){
            $exists = self::where($field, $prefix . self::generateRandomString(32 - strlen($prefix)))->first();
        }
        return $slug."-".$i;
    }
    
    public static function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';

	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }

	    return $randomString;
	}
}
