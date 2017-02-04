<?php namespace App\Http\Controllers;

use Auth;
use DB;
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
        return view('recommendations', ['activelink' => 'me']);
    }
    
    public function getRecommendation($slug){
        $recommendation = Recommendation::where('slug', $slug)
            ->first();
        
        $user = Auth::user();
        $recommendee = $recommendation->recommendee;
        $activelink = '';
        if ($recommendation){
            if ($user){
                if ($recommendation->recommendee_id == $user->id){
                    $activelink = 'me';
                    if ($recommendation->action == null){
                        $recommendation->action = 'viewed';
                        $recommendation->save();
                    }
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
        
        return view('recommendation', compact('activelink'));
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
        if ($r->episode->show){
            $ret['show_name']       = $r->episode->show->name;
            $ret['show_slug']       = $r->episode->show->slug;
        }
        $ret['action']              = $r->action;
        $ret['comment']             = $r->comment;
        
        return $ret;
    }
    
    public function apiGetRecommendationsPending(){
        return Auth::user()->recommendations_by_action('pending');
    }
    
    public function apiGetRecommendationsAccepted(){
        return Auth::user()->recommendations_by_action('accepted');
    }
    
    public function apiAcceptRecommendations(){
        if (Input::has('slugs')){
            return Recommendation::where('recommendee_id', Auth::user()->id)
                ->whereIn('slug', Input::get('slugs'))
                ->update(['action' => 'accepted']);
        }else{
            return response()->json(['message' => 'Bad Request - no slugs'], 400);
        }
    }
    
    public function apiRejectRecommendations(){
        if (Input::has('slugs')){
            return Recommendation::where('recommendee_id', Auth::user()->id)
                ->whereIn('slug', Input::get('slugs'))
                ->update(['action' => 'rejected']);
        }else{
            return response()->json(['message' => 'Bad Request - no slugs'], 400);
        }
    }
    
    public function apiMakeRecommendationsPending(){
        if (Input::has('slugs')){
            return Recommendation::where('recommendee_id', Auth::user()->id)
                ->whereIn('slug', Input::get('slugs'))
                ->update(['action' => 'viewed']);
        }else{
            return response()->json(['message' => 'Bad Request - no slugs'], 400);
        }
    }
}