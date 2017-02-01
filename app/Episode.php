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
    
    public function show(){
        return $this->belongsTo('App\Show');
    }
    
    public function prepare(){
        $this->howLongAgo = self::howLongAgo($this->pubdate);
        $this->pubdate_str = date('g:i A - j M Y', $this->pubdate);
        $this->description = strip_tags($this->description,"<p></p>");
        if (isset($this->likeddate)){
            $this->likedHowLongAgo = self::howLongAgo(strtotime($this->likeddate));
            $this->likeddate_str = date('g:i A - j M Y', strtotime($this->likeddate));
        }
        return $this;
    }
    
    public static function howLongAgo($pubdate){
        $short = false;
        $etime = time() - $pubdate;

        if ($etime < 1)      {
            return '0s';
        }

        $a = array( 365 * 24 * 60 * 60  =>  'year',
                   30 * 24 * 60 * 60  =>  'month',
                        24 * 60 * 60  =>  'day',
                             60 * 60  =>  'hour',
                                  60  =>  'minute',
                                   1  =>  'second'
                  );
        $a_units = array( 'year'   => array('short' => 'y', 'long' => 'year', 'longplural' => 'years'),
                         'month'  => array('short' => 'm', 'long' => 'month', 'longplural' => 'months'),
                         'day'    => array('short' => 'd', 'long' => 'day', 'longplural' => 'days'),
                         'hour'   => array('short' => 'h', 'long' => 'hour', 'longplural' => 'hours'),
                         'minute' => array('short' => 'm', 'long' => 'minute', 'longplural' => 'minutes'),
                         'second' => array('short' => 's', 'long' => 'second', 'longplural' => 'seconds')
                  );

        foreach ($a as $secs => $str){
            $d = $etime / $secs;
            if ($d >= 1){
                $r = round($d);
                if ($short){
                    return $r.$a_units[$str]['short'];
                }else{
                    return $r.' '.$a_units[$str][$r > 1 ? 'longplural' : 'long'].' ago';
                }
            }
        }
    }
}