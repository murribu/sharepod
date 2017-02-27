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
        $user = Auth::user();
        $episodes = Episode::join('playlist_episodes', 'playlist_episodes.episode_id', '=', 'episodes.id')
            ->leftJoin('likes as total_likes', function($join){
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
            ->leftJoin('shows', 'shows.id', '=', 'episodes.show_id')
            ->leftJoin(DB::raw('(select archived_episodes.id, url, slug, filesize, result_slug, episode_id from archived_episodes inner join archived_episode_users on archived_episodes.id = archived_episode_users.archived_episode_id where (result_slug is null or result_slug = \'ok\') and user_id = '.($user ? $user->id : DB::raw("-1")).' and active = 1 limit 1) ae'), 'ae.episode_id', '=', 'episodes.id')
            ->where('playlist_episodes.playlist_id', $this->id)
            ->selectRaw('episodes.show_id, episodes.id, episodes.slug, episodes.name, episodes.description, episodes.duration, episodes.explicit, coalesce(ae.filesize, episodes.filesize) filesize, episodes.img_url, episodes.pubdate, coalesce(ae.url, concat(\''.env('S3_URL').'/'.env('S3_BUCKET').'/episodes/\', ae.slug, \'.mp3\'), episodes.url) url, shows.name show_name, shows.slug show_slug, ae.result_slug, count(total_likes.id) as total_likes, count(this_user_likes.id) as this_user_likes, count(recommendations.id) total_recommendations, count(distinct pe.playlist_id) total_playlists, count(ae.id) this_user_archived')
            ->groupBy('episodes.show_id')
            ->groupBy('episodes.id')
            ->groupBy('episodes.slug')
            ->groupBy('episodes.name')
            ->groupBy('episodes.description')
            ->groupBy('episodes.duration')
            ->groupBy('episodes.explicit')
            ->groupBy('episodes.filesize')
            ->groupBy('ae.filesize')
            ->groupBy('episodes.img_url')
            ->groupBy('episodes.pubdate')
            ->groupBy('ae.url')
            ->groupBy('ae.slug')
            ->groupBy('episodes.url')
            ->groupBy('shows.name')
            ->groupBy('shows.slug')
            ->groupBy('ae.result_slug')
            ->groupBy('pe.ordering')
            ->groupBy('pe.id')
            ->orderBy('pe.ordering')
            ->orderBy('pe.id')
            ->get();

        foreach ($episodes as $e){
            $e = $e->prepare();
            unset($e->show);
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