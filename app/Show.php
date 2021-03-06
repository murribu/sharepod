<?php namespace App;

use Auth;
use DB;

use Illuminate\Database\Eloquent\Model;
use Exception;

class Show extends Model {
    
    use HasSlug;
    use HasLikes;
    use HasFeed;
    
    protected $fillable = ['name', 'description', 'category'];
    
    protected static $slug_reserved_words = ['new', 'list', 'search', 'undefined', 'feed', 'popular'];
    public static $like_type = 'show';
    
    public $table = 'shows';
    
    
    public function info_for_feed(){
        $ret = [];
        $ret['episodes'] = $this->episodes()->orderBy('pubdate', 'desc')->get();
        $ret['url'] = env('APP_URL')."/show/".$this->slug."/feed";
        $ret['name'] = $this->name;
        
        return $ret;
    }
    
    public function episodes(){
        return $this->hasMany('App\Episode')->where('active', 1);
    }
    
    public function limitedEpisodes($user = null, $limit = 10, $pubdate = false){
        $episodes = Episode::leftJoin('likes as total_likes', function($join){
                $join->on('total_likes.fk', '=', 'episodes.id');
                $join->on('total_likes.type', '=', DB::raw("'episode'"));
            })
            ->leftJoin('likes as this_user_likes', function($join) use ($user){
                $join->on('this_user_likes.user_id', '=', DB::raw($user ? $user->id : DB::raw("-1")));
                $join->on('this_user_likes.fk', '=', 'episodes.id');
                $join->on('this_user_likes.type', '=', DB::raw("'episode'"));
            })
            ->leftJoin('playlist_episodes as pe', 'pe.episode_id', '=', 'episodes.id')
            ->leftJoin('recommendations', 'recommendations.episode_id', '=', 'episodes.id')
            ->leftJoin(DB::raw('(select archived_episodes.id, url, slug, filesize, result_slug, episode_id from archived_episodes inner join archived_episode_users on archived_episodes.id = archived_episode_users.archived_episode_id where (result_slug is null or result_slug = \'ok\') and user_id = '.($user ? $user->id : DB::raw("-1")).' and active = 1) ae'), 'ae.episode_id', '=', 'episodes.id')
            ->selectRaw('episodes.show_id, episodes.id, episodes.slug, episodes.name, episodes.description, episodes.duration, episodes.explicit, coalesce(ae.url, case when ae.result_slug is null then null else concat(\''.env('S3_URL').'/'.env('S3_BUCKET').'/episodes/\', ae.slug, \'.mp3\') end, episodes.url) url, coalesce(ae.filesize, episodes.filesize) filesize, episodes.img_url, episodes.pubdate, ae.result_slug, count(distinct total_likes.id) as total_likes, count(distinct this_user_likes.id) as this_user_likes, count(distinct recommendations.id) total_recommendations, count(distinct pe.playlist_id) total_playlists, count(distinct ae.id) this_user_archived')
            ->where('episodes.active', 1)
            ->where('show_id', $this->id)
            ->orderBy('pubdate', 'desc')
            ->groupBy('episodes.id')
            ->groupBy('episodes.slug')
            ->groupBy('episodes.name')
            ->groupBy('episodes.description')
            ->groupBy('episodes.duration')
            ->groupBy('episodes.explicit')
            ->groupBy('episodes.filesize')
            ->groupBy('episodes.img_url')
            ->groupBy('episodes.pubdate')
            ->groupBy('episodes.show_id')
            ->groupBy('episodes.url')
            ->groupBy('ae.url')
            ->groupBy('ae.slug')
            ->groupBy('ae.filesize')
            ->groupBy('ae.result_slug')
            ->limit($limit);
        if ($pubdate){
            $episodes = $episodes->where('pubdate', '<', $pubdate);
        }
        
        $ret = $episodes->get();

        foreach($ret as $e){
            $e = $e->prepare();
            unset($e->show);
        }
        
        return $ret;
    }
    
    public function searchEpisodes($user = null, $searchText){
        $limit = 10;
        
        $episodes = Episode::leftJoin('likes as total_likes', function($join){
                $join->on('total_likes.fk', '=', 'episodes.id');
                $join->on('total_likes.type', '=', DB::raw("'episode'"));
            })
            ->leftJoin('likes as this_user_likes', function($join) use ($user){
                $join->on('this_user_likes.user_id', '=', DB::raw($user ? $user->id : DB::raw("-1")));
                $join->on('this_user_likes.fk', '=', 'episodes.id');
                $join->on('this_user_likes.type', '=', DB::raw("'episode'"));
            })
            ->leftJoin('playlist_episodes as pe', 'pe.episode_id', '=', 'episodes.id')
            ->leftJoin('recommendations', 'recommendations.episode_id', '=', 'episodes.id')
            ->leftJoin(DB::raw('(select archived_episodes.id, url, slug, filesize, result_slug, episode_id from archived_episodes inner join archived_episode_users on archived_episodes.id = archived_episode_users.archived_episode_id where (result_slug is null or result_slug = \'ok\') and user_id = '.($user ? $user->id : DB::raw("-1")).' and active = 1) ae'), 'ae.episode_id', '=', 'episodes.id')
            ->selectRaw('episodes.show_id, episodes.id, episodes.slug, episodes.name, episodes.description, episodes.duration, episodes.explicit, coalesce(ae.url, case when ae.result_slug is null then null else concat(\''.env('S3_URL').'/'.env('S3_BUCKET').'/episodes/\', ae.slug, \'.mp3\') end, episodes.url) url, coalesce(ae.filesize, episodes.filesize) filesize, episodes.img_url, episodes.pubdate, ae.result_slug, count(distinct total_likes.id) as total_likes, count(distinct this_user_likes.id) as this_user_likes, count(distinct recommendations.id) total_recommendations, count(distinct pe.playlist_id) total_playlists, count(distinct ae.id) this_user_archived')
            ->where('episodes.active', 1)
            ->where('show_id', $this->id)
            ->where(function($query) use ($searchText){
                $query->whereRaw('episodes.description like ?', ['%'.$searchText.'%']);
                $query->orWhereRaw('episodes.name like ?', ['%'.$searchText.'%']);
            })
            ->orderBy('pubdate', 'desc')
            ->groupBy('episodes.id')
            ->groupBy('episodes.slug')
            ->groupBy('episodes.name')
            ->groupBy('episodes.description')
            ->groupBy('episodes.duration')
            ->groupBy('episodes.explicit')
            ->groupBy('episodes.filesize')
            ->groupBy('episodes.img_url')
            ->groupBy('episodes.pubdate')
            ->groupBy('episodes.show_id')
            ->groupBy('episodes.url')
            ->groupBy('ae.url')
            ->groupBy('ae.slug')
            ->groupBy('ae.filesize')
            ->groupBy('ae.result_slug')
            ->limit($limit)
            ->get();
            
        foreach($episodes as $e){
            $e = $e->prepare();
            unset($e->show);
        }
        
        $count = Episode::where('show_id', $this->id)
            ->where(function($query) use ($searchText){
                $query->whereRaw('episodes.description like ?', ['%'.$searchText.'%']);
                $query->orWhereRaw('episodes.name like ?', ['%'.$searchText.'%']);
            })
            ->count();
        
        return compact('episodes', 'count');
    }
    
    public static function testFeed($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $str = curl_exec($ch);
        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case 200:  # OK
                    break;
                default:
                    curl_close($ch);
                    return ['error' => 1, 'message' => 'That URL produced an HTTP error -  code: '. $http_code];
            }
        }
        curl_close($ch);

        $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8'); // This removes nasty characters
        $str = str_replace("itunes:","itunes_",$str);
        $str = str_replace("sy:","sy_",$str);
        
        if (@$content = simplexml_load_string($str)){
            if (!isset($content->channel)){
                return ['error' => 1, 'message' => 'The Feed must start with a \'channel\' node'];
            }
            if (!isset($content->channel->title)){
                return ['error' => 1, 'message' => 'The \'channel\' node must have a title'];
            }
            if (count($content->channel->item) == 0){
                return ['error' => 1, 'message' => 'This podcast has no episodes'];
            }
            $i = 0;
			foreach($content->channel->item as $item){
				$guid = (string)$item->guid;
			    $exists = Episode::join('shows', 'shows.id', '=', 'episodes.show_id')
                    ->where('shows.active', '1')
                    ->where('episodes.guid', $guid)
                    ->first();
			    if ($exists){
			        return ['error' => 'already_exists', 'slug' => $exists->show->slug, 'name' => $exists->show->name];
			    }
			    if ($i++ == 10){
			        return ['success' => 1];
			    }
			}
        }else{
            return ['error' => 1, 'message' => 'We could not parse the result from this URL. Please make sure you\'re using the podcast\'s RSS feed.'];
        }
    }
    
    public function parseFeed(){
        $newEpisodes = 0;
        if ($this->feed != ""){
            //$str = file_get_contents($this->feed);
            //I had to change the file_get_contents to a curl call because SquareSpace blocks requests based on useragent. So, I'm spoofing a useragent. Done.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->feed);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $str = curl_exec($ch);
            curl_close($ch);

            $str = self::stripInvalidXml($str); // This removes nasty characters
            $str = str_replace("itunes:","itunes_",$str);
            $str = str_replace("sy:","sy_",$str);
            if ($content = simplexml_load_string($str)){
				//Update podcast info
				$this->name = $content->channel->title;
                if (!$this->slug || $this->slug == ''){
                    $this->slug = self::findSlug($content->channel->title);
                }
                $this->save();
				$this->url = $content->channel->link;
				$this->description = $content->channel->description;
				if ($content->channel->image->url){
					$this->img_url = $content->channel->image->url;
				}elseif ($content->channel->itunes_image){
					foreach($content->channel->itunes_image->Attributes() as $key=>$val){
						if ($key == "href"){
							$this->img_url = (string)$val;
						}
					}
				}
				if ($content->channel->sy_updatePeriod){
				    $this->updatePeriod = (string)$content->channel->sy_updatePeriod;
				}
				if ($content->channel->sy_updateFrequency){
				    $this->updateFrequency = (string)$content->channel->sy_updateFrequency;
				}
				if ($content->channel->itunes_category){
					foreach($content->channel->itunes_category->Attributes() as $key=>$val){
					    if ($key == "text"){
        				    $this->category = (string)$val;
					    }
					}
				}
				$this->active = true;
				$this->save();
				foreach($content->channel->item as $item){
					$guid = (string)$item->guid;
					$episode = Episode::where('show_id', $this->id)->where('guid', $guid)->first();
						
					if($episode){
						//This will stop the proc from checking once it sees a guid that is already in the db. This assumes that new episodes will be at the top
						break;
					}else{
						$episode = new Episode();
						$episode->show_id = $this->id;
						$episode->pubdate = strtotime((string)$item->pubDate);
						$episode->name = (string)$item->title;
                        $episode->slug = Episode::findSlug($this->name."-".$episode->name);
						$episode->description = (string)$item->description;
						//remove emojis
						$episode->description = preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $episode->description);
						$episode->duration = 0;
						if ($item->itunes_duration){
							$duration = explode(":",(string)$item->itunes_duration);
							switch (count($duration)){
								case 3:
									$episode->duration = $duration[2] + 60*$duration[1] + 3600*$duration[0];
									break;
								case 2:
									$episode->duration = $duration[1] + 60*$duration[0];
									break;
								case 1:
									$episode->duration = $duration[0];
									break;
							}
						}
						if ($item->itunes_explicit){
							$episode->explicit = (string)$item->itunes_explicit == "1" || (string)$item->itunes_explicit == "yes";
						}else{
							$episode->explicit = false;
						}
						$episode->link = (string)$item->link;
						if ($item->itunes_image){
							foreach($item->itunes_image->Attributes() as $key=>$val){
								if ($key == "href")
									$episode->img_url = (string)$val;
							}
						}
						if ($item->enclosure && $item->enclosure->Attributes()){
							foreach($item->enclosure->Attributes() as $key=>$val){
								if($key == "length"){
									$val = (string)$val;
									$episode->filesize = $val == "" ? null : $val;
								}
								if($key == "url")
									$episode->url = (string)$val;
							}
						}
						$episode->guid = $guid;
						$episode->save();
						$newEpisodes++;
					}
				}
			}else{
				$this->active = false;
				$this->save();
			}
        }else{
            throw new Exception('Empty RSS Feed URL');
        }
    }
    
    public static function updateOneFeed($id = false){
        if ($id){
            $show = Show::find($id);
        }else{
            $show = Show::where('updated_at', '<', DB::raw('date_sub(now(), interval 1 hour)'))
                ->where('active', '1')
                ->orderBy('updated_at')
                ->first();
        }
        if ($show){
            // var_dump([$show->name]);
            $ret = $show->parseFeed();
            return $ret;
        }
    }
    
    public function prepare(){
        $this->description = strip_tags($this->description,"<p></p>");
        if (isset($this->likeddate)){
            $this->likedHowLongAgo = self::howLongAgo(strtotime($this->likeddate));
            $this->likeddate_str = date('g:i A - j M Y', strtotime($this->likeddate));
        }
    }
    
    public static function popular($user, $category = null){
        $limit = 10;
        $user_id = Auth::user() ? Auth::user()->id : -1;
        $shows = Show::selectRaw("shows.id, shows.name, shows.slug, shows.img_url, shows.description,
            (
            9 * (select count(id) from likes
                where fk = shows.id
                    and type = 'show'
                    and (user_id in (select recommender_id from connections where user_id = $user_id and status = 'approved')
                        or
                        user_id = $user_id)
            ) +
            (select count(id) from likes
                where fk = shows.id
                    and type = 'show')
            ) score")
        ->where('active', '1')
        ->orderBy('score', 'desc')
        ->limit($limit);
        
        if ($category){
            $shows = $shows->where('category', $category);
        }
        return $shows->get();
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

    /**
     * Removes invalid XML
     *
     * @access public
     * @param string $value
     * @return string
     */
    public static function stripInvalidXml($value)
    {
        $ret = "";
        $current;
        if (empty($value)) 
        {
            return $ret;
        }

        $length = strlen($value);
        for ($i=0; $i < $length; $i++)
        {
            $current = ord($value{$i});
            if (($current == 0x9) ||
                ($current == 0xA) ||
                ($current == 0xD) ||
                (($current >= 0x20) && ($current <= 0xD7FF)) ||
                (($current >= 0xE000) && ($current <= 0xFFFD)) ||
                (($current >= 0x10000) && ($current <= 0x10FFFF)))
            {
                $ret .= chr($current);
            }
            else
            {
                $ret .= " ";
            }
        }
        return $ret;
    }
}