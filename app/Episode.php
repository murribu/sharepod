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
    protected static $slug_reserved_words = ['new', 'search', 'undefined', 'popular'];
    
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
        unset($this->id);
        return $this;
    }
    
    public function friend_recommenders($user){
        return DB::select('select u.slug, u.name
            from users u
            inner join recommendations r on r.episode_id = ? and r.recommender_id in (
                select recommender_id from connections
                        where user_id = ?
                        and status = \'approved\'
                )
            order by r.created_at desc
            limit 5
        ', [$this->id, $user->id]);
    }
    
    public function likers($user = null){
        return DB::select('select u.slug, u.name
            from users u
            inner join likes l on l.fk = ? and l.type = \'episode\' and u.id = l.user_id
            left join connections c on c.recommender_id = u.id and c.user_id = ? and status = \'approved\'
            order by case when c.id is null then 1 else 0 end, l.created_at desc
            limit 5
        ', [$this->id, $user ? $user->id : -1]);
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
    
    public static function popular($limit = 10){
        $user_id = Auth::user() ? Auth::user()->id : -1;
        $vars = [$user_id, $user_id, $limit];
        $episodes = Episode::selectRaw("episodes.id, episodes.name, episodes.slug, episodes.description, episodes.img_url, episodes.pubdate, s.name show_name, s.slug show_slug, 
            (
            100 * (select count(id) from likes 
                where fk = episodes.id 
                    and type = 'episode' 
                    and created_at > date_sub(now(), interval 1 week)
                    and user_id in (select recommender_id from connections where user_id = $user_id and status = 'approved')
            ) +
            10 * (select count(id) from likes 
                where fk = s.id 
                    and type = 'show' 
                    and created_at > date_sub(now(), interval 1 week)
                    and user_id in (select recommender_id from connections where user_id = $user_id and status = 'approved')
            ) +
            5 * (select count(id) from likes 
                where fk = episodes.id 
                    and type = 'episode' 
                    and created_at > date_sub(now(), interval 1 week)
            ) +
            (select count(id) from likes 
                where fk = s.id 
                    and type = 'show' 
                    and created_at > date_sub(now(), interval 1 week)
            ) +
            200 * (select count(id) from recommendations
                where episode_id = episodes.id
                and created_at > date_sub(now(), interval 1 week)
                and recommender_id in (select recommender_id from connections where user_id = $user_id and status = 'approved')
            ) +
            50 * (select count(id) from recommendations
                where episode_id = episodes.id
                and created_at > date_sub(now(), interval 1 week)
            )
            ) * TIMESTAMPDIFF(SECOND, '2000-1-1', least(episodes.created_at, from_unixtime(episodes.pubdate))) score")
        ->leftJoin('shows as s', 's.id', '=', 'episodes.show_id')
        ->groupBy('episodes.id')
        ->groupBy('episodes.name')
        ->groupBy('episodes.slug')
        ->groupBy('episodes.description')
        ->groupBy('episodes.img_url')
        ->groupBy('episodes.pubdate')
        ->groupBy('s.id')
        ->groupBy('s.name')
        ->groupBy('s.slug')
        ->groupBy('episodes.created_at')
        ->orderBy('score', 'desc')
        ->limit($limit)
        ->get();
        
        
        foreach($episodes as $e){
            unset($e->score);
            $e->likers = $e->likers(Auth::user());
            if (Auth::user()){
                $e->friend_recommenders = $e->friend_recommenders(Auth::user());
            }
            $e->prepare();
        }
        
        return $episodes;
        /**
            (
                200 * friend_recommendations            + 
                100 * friend_elikes                     +
                 50 * recommendations                   + 
                 10 * friend_slikes                     + 
                  5 * elikes                            +
                  1 * slikes
              ) * (9999999999 - TIMESTAMPDIFF(SECOND, '2000-1-1', e.created_at)) score
        **/
    }
}