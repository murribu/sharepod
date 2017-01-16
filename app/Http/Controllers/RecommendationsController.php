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
            ->whereNull('action')
            ->first();
        
        $recommendation->action = 'viewed';
        $recommendation->save();
        
        $user = Auth::user();
        $recommendee = $recommendation->recommendee;
        
        if ($recommendation){
            if ($user){
                if ($recommendation->recommendee_id != $user->id){
                    // This recommendation doesn't belong to the current user
                    $msg = 'This recommendation does not belong to you';
                }
            }else{
                if (!$recommendation->recommendee->verified){
                    // Not logged in. The recommendee is not verified
                    $recommendee->verified = true;
                    $recommendee->save();
                }
                Auth::login($recommendee);
            }
        }else{
            // Recommendation doesn't exist or has already been viewed.
        }
        
        return view('recommendation');
    }
    
    public function getRecentRecommendees(){
        $user = Auth::user();
        
        return $user->recent_recommendees();
    }
}