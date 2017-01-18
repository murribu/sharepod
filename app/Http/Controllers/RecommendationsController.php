<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Episode;
use App\Recommendation;

class RecommendationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified')->only('getRecentRecommendees', 'apiGetRecentRecommendationsGiven');
    }
    
    public function getRecommendations(){
        return view('recommendations', ['activelink' => 'recommendations']);
    }
    
    public function getRecommendation($slug){
        $recommendation = Recommendation::where('slug', $slug)
            ->first();
        
        $user = Auth::user();
        $recommendee = $recommendation->recommendee;
        
        if ($recommendation){
            if ($user){
                if ($recommendation->recommendee_id == $user->id){
                    $recommendation->action = 'viewed';
                    $recommendation->save();
                }
            }else{
                if (!$recommendee->verified){
                    // Not logged in. The recommendee is not verified
                    $recommendee->verified = true;
                    $recommendee->save();
                }
                if ($recommendation->action == null){
                    Auth::login($recommendee);
                    $recommendation->action = 'viewed';
                    $recommendation->save();
                }
            }
        }else{
            // Recommendation doesn't exist.
        }
        
        return view('recommendation', ['activelink' => 'recommendations']);
    }
    
    public function apiGetRecentRecommendees(){
        return Auth::user()->recent_recommendees();
    }
    
    public function apiGetRecommendation($slug){
        $r = Recommendation::where('slug', $slug)->first();
        $ret = [];
        $ret['slug'] = $r['slug'];
        $ret['recommender']         = $r->recommender->name;
        $ret['recommender_slug']    = $r->recommender->slug;
        $ret['recommendee']         = $r->recommendee->name;
        $ret['recommendee_slug']    = $r->recommendee->slug;
        $ret['episode_name']        = $r->episode->name;
        $ret['episode_slug']        = $r->episode->slug;
        $ret['comment']             = $r->comment;
        
        return $ret;
    }
    
    public function apiGetRecommendationsGiven(){
        return Auth::user()->recent_recommendations_given(Input::has('date') ? Input::get('date') : '2199-12-31 23:23:59', Input::has('episodes') ? Input::get('episodes') : '-1');
    }
    
    public function apiGetRecommendationsGivenCount(){
        return Auth::user()->recommendations_given_count();
    }
    
    public function apiGetRecommendationsReceived(){
        return Auth::user()->recent_recommendations_received(Input::has('date') ? Input::get('date') : '2199-12-31 23:23:59', Input::has('episodes') ? Input::get('episodes') : '-1');
    }
    
    public function apiGetRecommendationsReceivedCount(){
        return Auth::user()->recommendations_received_count();
    }
    
    public function apiGetRecommendationsPending(){
        return Auth::user()->recommendations_pending();
    }
}