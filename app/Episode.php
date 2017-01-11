<?php namespace App;
use DB;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model {
    
    use HasSlug;
    use HasLikes;
    
    public $table = 'episodes';
    public static $like_type = 'episode';
    
    public function send_via_email($email_address){
        $user           = Auth::user();
        $to_name        = $email_address;
        $email_address  = $email_address;
        $from_name      = $user->name;
        $subject        = $user->name.' has recommended a podcast episode';
        $link           = env('APP_URL').'/accept_recommendation?token=';
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
    
	public function img_url_default(){
		if ($this->img_url){
			return $this->img_url;
		}else{
			return $this->podcast->img_url;
		}
	}
}