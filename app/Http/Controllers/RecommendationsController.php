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
        $this->middleware('verified');
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
                }elseif ($recommendation->recommender_id != $user->id){
                    // This recommendation doesn't belong to the current user
                    $msg = 'This recommendation does not belong to you';
                }
            }else{
                if (!$recommendee->verified){
                    // Not logged in. The recommendee is not verified
                    $recommendee->verified = true;
                    $recommendee->save();
                }
                Auth::login($recommendee);
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
}