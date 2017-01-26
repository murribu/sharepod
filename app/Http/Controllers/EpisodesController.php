<?php namespace App\Http\Controllers;

use Auth;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Episode;

class EpisodesController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified');
    }
    
    public function getEpisode(){
        return view('episode', ['activelink' => 'shows']);
    }
    
    public function apiGetEpisode($slug){
        $user = Auth::user();
        return Episode::leftJoin('likes', function($join){
            $join->on('likes.fk', '=', 'episodes.id');
            $join->on('likes.type', '=', DB::raw("'episode'"));
        })
        ->leftJoin('likes as this_user_likes', function($join) use ($user){
            $join->on('this_user_likes.fk', '=', 'episodes.id');
            $join->on('this_user_likes.type', '=', DB::raw("'episode'"));
            $join->on('this_user_likes.user_id', '=', DB::raw($user ? $user->id : DB::raw("-1")));
        })
        ->leftJoin('shows as s', 's.id', '=', 'episodes.show_id')
        ->leftJoin('recommendations', 'recommendations.episode_id', '=', 'episodes.id')
        ->selectRaw('episodes.name, episodes.description, episodes.slug, s.name as show_name, s.slug as show_slug, count(this_user_likes.id) this_user_likes, count(likes.id) total_likes, count(recommendations.id) total_recommendations')
        ->groupBy('episodes.name')
        ->groupBy('episodes.description')
        ->groupBy('episodes.slug')
        ->groupBy('s.name')
        ->groupBy('s.slug')
        ->where('episodes.slug', $slug)
        ->first();
    }
    
    public function recommend(){
        $user = Auth::user();
        $recommendation = $user->recommend(Input::all());
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
        
        return ['success' => 1, 'recommendation_slug' => $recommendation->slug];
    }
    
    public function apiLike(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        if ($ep->like(Auth::user())){
            return ['success' => 1, 'total_likes' => $ep->likeCount(), 'this_user_likes' => 1];
        }
    }
    
    public function apiUnlike(){
        $ep = Episode::where('slug', Input::get('slug'))->first();
        if ($ep->unlike(Auth::user())){
            return ['success' => 1, 'total_likes' => $ep->likeCount(), 'this_user_likes' => 0];
        }
    }
}