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
    
    public function apiGetEpisodeLikers($slug){
        return Episode::where('slug', $slug)->first()->likers(Auth::user());
    }
    
    public function getEpisode(){
        return view('episode', ['activelink' => 'shows']);
    }
    
    public function apiGetEpisode($slug){
        $e = Episode::where('episodes.active', 1)
            ->where('episodes.slug', $slug)
            ->first();
        $e->stats = $e->stats(Auth::user());
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
                'stats' => $recommendation->episode->stats(Auth::user()),
            ];
        }else{
            return response()->json(['message' => 'You have reached today\'s maximum number of Recommendations for your Subscription Plan'], 403);
        }
    }
    
    public function apiLike(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        if ($ep->like(Auth::user())){
            return ['success' => 1, 'stats' => $ep->stats(Auth::user())];
        }else{
            return response()->json(['message' => 'Liking Error'], 500);
        }
    }
    
    public function apiUnlike(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        if ($ep->unlike(Auth::user())){
            return ['success' => 1, 'stats' => $ep->stats(Auth::user())];
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
            $ret['stats'] = $ep->stats(Auth::user());
            return $ret;
        }
    }
    
    public function apiUnarchive(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        $ret = $ep->unarchive(Auth::user());
        if (isset($ret['error'])){
            return response()->json(['message' => $ret['message']], 500);
        }else{
            $ret['stats'] = $ep->stats(Auth::user());
            return $ret;
        }
    }
}