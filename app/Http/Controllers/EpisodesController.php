<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;
use Mail;

use App\Episode;

class EpisodesController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified');
    }
    
    public function send(){
        $user = Auth::user();
        
        $to_name        = Input::get('email_address');
        $email_address  = Input::get('email_address');
        $from_name      = $user->name;
        $subject        = $user->name.' has recommended a podcast episode';
        $link           = env('APP_URL').'/accept_recommendation?token=asdf';
        if (Input::has('to_name')){
            $to_name = Input::get('to_name');
        }
        Mail::send('emails.send_episode', compact('to_name', 'from_name', 'link'), function($message) use ($email_address, $to_name, $subject) {
            $message->to($email_address, $to_name)
            ->subject($subject);
            $message->from(env('MAILGUN_FROM_EMAIL_ADDRESS', 'shaare.pod@gmail.com'), env('APP_NAME'));
        });
        return ['success', '1'];
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