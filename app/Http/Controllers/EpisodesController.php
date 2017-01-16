<?php namespace App\Http\Controllers;

use Auth;
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