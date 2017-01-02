<?php
namespace App;

trait HasSlug{
    
    public static function findSlug($slug = false){
        $field = property_exists( new self, 'slug_field') ? self::$slug_field : 'slug';
        $reserved_words = property_exists( new self, 'slug_reserved_words') ? self::$slug_reserved_words : ['new'];
        $limit = property_exists( new self, 'slug_limit') ? self::$slug_limit : 255;
        if ($slug){
            $slug = substr(strtolower(preg_replace("/[^a-zA-Z\d]/", "-", $slug)), 0, ($limit - 5));
        }else{
            $slug = self::generateRandomString(32);
        }
        $exists = self::where($field, $slug)->first();
        if (!$exists && !in_array($slug, $reserved_words)){
            return $slug;
        }
        $i = 0;
        while ($exists || in_array($slug, $reserved_words)){
            $exists = self::where($field, $slug."-".++$i)->first();
        }
        return $slug."-".$i;
    }
    
    public static function findSlugWithPrefix($prefix){
        $field = property_exists( new self, 'slug_field') ? self::$slug_field : 'slug';
        $reserved_words = property_exists( new self, 'slug_reserved_words') ? self::$slug_reserved_words : ['new'];
        $limit = property_exists( new self, 'slug_limit') ? self::$slug_limit : 255;
        if (strlen($prefix) >= 30){
            throw new Exception("Prefix must be shorter than 30 characters");
        }
        $slug = substr($prefix . self::generateRandomString(32 - strlen($prefix)), 0, ($limit - 5));
        $exists = self::where($field, $slug)->first();
        if (!$exists && !in_array($slug, $reserved_words)){
            return $slug;
        }
        $i = 0;
        while ($exists || in_array($slug, $reserved_words)){
            $exists = self::where($field, substr($prefix . self::generateRandomString(32 - strlen($prefix)), 0, ($limit - 5)))->first();
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
