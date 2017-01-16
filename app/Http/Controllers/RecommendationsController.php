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
        $this->middleware('verified')->only('getRecentRecommendees');
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
        
        return view('recommendation');
    }
    
    public function getRecentRecommendees(){
        $user = Auth::user();
        
        return $user->recent_recommendees();
    }
    
    public function apiGetRecommendation($slug){
        $r = Recommendation::where('slug', $slug)->first();
        $ret = [];
        $ret['slug'] = $r['slug'];
        $ret['recommender'] = $r->recommender->name;
        $ret['recommendee'] = $r->recommendee->name;
        
        return $ret;
    }
}