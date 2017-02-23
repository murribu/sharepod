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
    
    public function cronArchiveEpisode(){
        //meant to be called from a cronjob
        //todo - But it needs to communicate back to a Controller so that the controller can send the user a notification about whether the archiving was successful
        $lockfile = "/tmp/episode_archive.lock";

        if(!file_exists($lockfile))
            $fh = fopen($lockfile, "w");
        else
            $fh = fopen($lockfile, "r");

        if($fh === FALSE) exit("Unable to open lock file");

        if(!flock($fh, LOCK_EX)) // another process is running
            exit("Lock file already in use");
            
        $ae = ArchivedEpisode::with('episode')
            ->where('active', '1')
            ->whereNull('status_code')
            ->whereNotNull('episode_id')
            ->first();
        if ($ae){
            $plan = $user->plan();
            if ($plan && $plan == env('PLAN_BASIC_NAME')){
                $limit = intval(env('PLAN_BASIC_STORAGE_LIMIT'));
            }
            if ($plan && $plan == env('PLAN_PREMIUM_NAME')){
                $limit = intval(env('PLAN_PREMIUM_STORAGE_LIMIT'));
            }
            $ae->filesize = File::size($local_location);
            if ($user->storage() + File::size($local_location) > $limit){
                $ae->active = 0;
                $ae->message = 'The user has reached their storage limit';
                $ae->save();
                flock($fh, LOCK_UN);
                return ['error' => 1, 'message' => 'You have reached your storage limit'];
            }
        }
    }
    
    public function getEpisode(){
        return view('episode', ['activelink' => 'shows']);
    }
    
    public function apiGetEpisode($slug){
        $user = Auth::user();
        $e = Episode::leftJoin('likes', function($join){
            $join->on('likes.fk', '=', 'episodes.id');
            $join->on('likes.type', '=', DB::raw("'episode'"));
        })
        ->leftJoin('likes as this_user_likes', function($join) use ($user){
            $join->on('this_user_likes.fk', '=', 'episodes.id');
            $join->on('this_user_likes.type', '=', DB::raw("'episode'"));
            $join->on('this_user_likes.user_id', '=', DB::raw($user ? $user->id : DB::raw("-1")));
        })
        ->leftJoin('playlist_episodes as pe', 'pe.episode_id', '=', 'episodes.id')
        ->leftJoin('shows as s', 's.id', '=', 'episodes.show_id')
        ->leftJoin('recommendations', 'recommendations.episode_id', '=', 'episodes.id')
        ->selectRaw('episodes.id, episodes.pubdate, episodes.name, episodes.description, episodes.slug, s.name as show_name, s.slug as show_slug, count(this_user_likes.id) this_user_likes, count(likes.id) total_likes, count(recommendations.id) total_recommendations, count(distinct pe.playlist_id) total_playlists')
        ->groupBy('episodes.id')
        ->groupBy('episodes.pubdate')
        ->groupBy('episodes.name')
        ->groupBy('episodes.description')
        ->groupBy('episodes.slug')
        ->groupBy('s.name')
        ->groupBy('s.slug')
        ->where('episodes.slug', $slug)
        ->first();
        
        return $e->prepare();
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
}