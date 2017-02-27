<?php namespace App\Http\Controllers;

use Auth;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use Laravel\Spark\Contracts\Repositories\NotificationRepository;

use App\Episode;
use App\Recommendation;

class EpisodesController extends Controller
{
    public function __construct(NotificationRepository $notifications)
    {
        $this->middleware('verified')->except(['apiGetPopular', 'getEpisode', 'apiGetEpisode']);
        $this->notifications = $notifications;
    }
    
    public function getEpisode(){
        return view('episode', ['activelink' => 'shows']);
    }
    
    public function apiGetEpisode($slug){
        $user = Auth::user();
        $e = Episode::leftJoin('likes as total_likes', function($join){
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
            ->selectRaw('episodes.show_id, episodes.id, episodes.slug, episodes.name, episodes.description, episodes.duration, episodes.explicit, coalesce(ae.url, concat(\''.env('S3_URL').'/'.env('S3_BUCKET').'/episodes/\', ae.slug, \'.mp3\'), episodes.url) url, coalesce(ae.filesize, episodes.filesize) filesize, episodes.img_url, episodes.pubdate, ae.result_slug, count(total_likes.id) as total_likes, count(this_user_likes.id) as this_user_likes, count(recommendations.id) total_recommendations, count(distinct pe.playlist_id) total_playlists, count(ae.id) this_user_archived')
            ->where('episodes.active', 1)
            ->where('episodes.slug', $slug)
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
        ->first();
        
        $e = $e->prepare();
        unset($e->show);
        return $e;
    }
    
    public function recommend(){
        $user = Auth::user();
        $permissions = $user->plan_permissions();
        if ($permissions['can_recommend']){
            $recommendation = $user->recommend(Input::all(), $this->notifications);
            if (isset($recommendation['error'])){
                return response()->json(['message' => $recommendation['message']], $recommendation['error']);
            }
            
            switch ($recommendation->status){
                case 'rejected':
                    $message = 'You have been blocked from recommending episodes to this user';
                    break;
                case 'accepted':
                    $message = 'Success! You have recommended this episode to '.$recommendation->recommendee->name;
                    break;
                default:
                    $message = 'Success! You have recommended this episode to '.$recommendation->recommendee->name.' They will be notified.';
                    break;
            }
            
            $rec_count = Recommendation::where('episode_id', $recommendation->episode->id)
                ->count();
            
            return [
                'success'               => 1,
                'recommendation_slug'   => $recommendation->slug,
                'total_recommendations' => $rec_count
            ];
        }else{
            return response()->json(['message' => 'You have reached today\'s maximum number of Recommendations for your Subscription Plan'], 403);
        }
    }
    
    public function apiLike(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        if ($ep->like(Auth::user())){
            return ['success' => 1, 'total_likes' => $ep->likeCount(), 'this_user_likes' => 1];
        }else{
            return response()->json(['message' => 'Liking Error'], 500);
        }
    }
    
    public function apiUnlike(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        if ($ep->unlike(Auth::user())){
            return ['success' => 1, 'total_likes' => $ep->likeCount(), 'this_user_likes' => 0];
        }else{
            return response()->json(['message' => 'Unliking Error'], 500);
        }
    }
    
    public function apiGetPopular(){
        return Episode::popular();
    }
    
    public function apiArchive(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        $ret = $ep->request_archive(Auth::user());
        if (isset($ret['error'])){
            return response()->json(['message' => $ret['message']], 500);
        }else{
            return $ret;
        }
    }
    
    public function apiUnarchive(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        $ret = $ep->unarchive(Auth::user());
        if (isset($ret['error'])){
            return response()->json(['message' => $ret['message']], 500);
        }else{
            return $ret;
        }
    }
}