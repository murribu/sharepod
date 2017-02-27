<?php namespace App;
use Auth;
use DB;
use Mail;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model {
    
    use HasSlug;
    use HasLikes;
    use HasFeed;
    
    public $table = 'playlists';
    public static $like_type = 'playlist';
    protected static $slug_reserved_words = ['new', 'popular'];
    
    public function episodes(){
        $self = $this;
        
        $episodes = Episode::join('playlist_episodes', 'playlist_episodes.episode_id', '=', 'episodes.id')
            ->leftJoin('shows', 'shows.id', '=', 'episodes.show_id')
            ->leftJoin(DB::raw('(select episode_id, filesize, url, slug from archived_episodes where result_slug = \'ok\' and id in (select archived_episode_id from archived_episode_users where active = 1 and user_id = '.$this->user_id.')) ae'), 'ae.episode_id', '=', 'episodes.id')
            ->where('playlist_episodes.playlist_id', $this->id)
            ->selectRaw('episodes.show_id, episodes.id, episodes.slug, episodes.name, episodes.description, episodes.duration, episodes.explicit, coalesce(ae.filesize, episodes.filesize) filesize, episodes.img_url, episodes.pubdate, coalesce(ae.url, concat(\''.env('S3_URL').'/'.env('S3_BUCKET').'/episodes/\', ae.slug, \'.mp3\'), episodes.url) url, shows.name show_name, shows.slug show_slug')
            ->orderBy('ordering')
            ->orderBy('id')
            ->get();

        foreach ($episodes as $e){
            $e = $e->prepare();
        }
        
        return $episodes;
    }
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function info_for_feed(){
        $ret = [];
        $ret['episodes'] = $this->episodes();
        $ret['url'] = env('APP_URL')."/playlists/".$this->slug."/feed";
        $ret['name'] = $this->name;
        
        return $ret;
    }
}