<?php namespace App;

use DB;

use Illuminate\Database\Eloquent\Model;
use Exception;

class Show extends Model {
    
    use HasSlug;
    use HasLikes;
    
    protected $slug_reserved_words = ['new', 'list', 'search', 'undefined'];
    
    public $table = 'shows';
    
    public function episodes(){
        return $this->hasMany('App\Episode')->where('active', 1);
    }
    
    public function limitedEpisodes($limit, $pubdate = false){
        $episodes = Episode::where('show_id', $this->id)
            ->select('slug', 'name', 'description', 'duration', 'explicit', 'filesize', 'img_url', 'pubdate')
            ->where('active', 1)
            ->orderBy('pubdate', 'desc')
            ->limit($limit);
        if ($pubdate){
            $episodes = $episodes->where('pubdate', '<', $pubdate);
        }
        
        return $episodes->get();
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
                $this->slug = self::findSlug($content->channel->title);
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
}