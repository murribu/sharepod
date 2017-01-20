<?php namespace App;

use Auth;
use DB;

use Illuminate\Database\Eloquent\Model;
use Exception;

class Show extends Model {
    
    use HasSlug;
    use HasLikes;
    
    protected static $slug_reserved_words = ['new', 'list', 'search', 'undefined'];
    public static $like_type = 'show';
    
    public $table = 'shows';
    
    public function episodes(){
        return $this->hasMany('App\Episode')->where('active', 1);
    }
    
    public function limitedEpisodes($user = null, $limit = 10, $pubdate = false){
        $episodes = Episode::leftJoin('likes as total_likes', function($join) use ($user){
                $join->on('total_likes.fk', '=', 'episodes.id');
                $join->on('total_likes.type', '=', DB::raw("'episode'"));
            })
            ->leftJoin('likes as this_user_likes', function($join) use ($user){
                $join->on('this_user_likes.user_id', '=', DB::raw($user ? $user->id : DB::raw("-1")));
                $join->on('this_user_likes.fk', '=', 'episodes.id');
                $join->on('this_user_likes.type', '=', DB::raw("'episode'"));
            })
            ->leftJoin('recommendations', 'recommendations.episode_id', '=', 'episodes.id')
            ->where('show_id', $this->id)
            ->selectRaw('episodes.id, episodes.slug, episodes.name, episodes.description, episodes.duration, episodes.explicit, episodes.filesize, episodes.img_url, episodes.pubdate, count(total_likes.id) as total_likes, count(this_user_likes.id) as this_user_likes, count(recommendations.id) total_recommendations')
            ->where('active', 1)
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
            ->limit($limit);
        if ($pubdate){
            $episodes = $episodes->where('pubdate', '<', $pubdate);
        }
        
        $ret = $episodes->get();
        
        foreach($ret as $e){
            $e->howLongAgo = self::howLongAgo($e->pubdate);
            $e->pubdate_str = date('g:i A - j M Y', $e->pubdate);
            $e->description = strip_tags($e->description,"<p></p>");
        }
        
        return $ret;
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
            $str = curl_exec($ch);
            curl_close($ch);
              
            $str = str_replace("itunes:","itunes_",$str);
            if (@$content = simplexml_load_string($str)){
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
								if($key == "length")
									$episode->filesize = (string)$val;
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
            return $show->parseFeed();
        }
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