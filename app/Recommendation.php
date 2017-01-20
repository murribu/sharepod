<?php namespace App;

use Mail;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model {
    
    use HasSlug;
    
    protected static $slug_length = 64;
    protected static $slug_reserved_words = ['accept', 'reject', 'make_pending', 'new'];
    
    public $table = 'recommendations';
    
    public function send_via_email(){
        $to_name        = $this->recommendee->name;
        $email_address  = $this->recommendee->email;
        $from_name      = $this->recommender->name;
        $subject        = $this->recommender->name.' has recommended a podcast episode';
        $send_to_user   = User::first_or_create_to_send_via_email($email_address);
        $link           = env('APP_URL').'/recommendations/'.$this->slug;
        
        if (isset($input['to_name'])){
            $to_name = Input::get('to_name');
        }
        Mail::send('emails.send_episode', compact('to_name', 'from_name', 'link'), function($message) use ($email_address, $to_name, $subject) {
            $message->to($email_address, $to_name)
            ->subject($subject);
            $message->from(env('MAILGUN_FROM_EMAIL_ADDRESS', 'shaare.pod@gmail.com'), env('APP_NAME'));
        });
        return ['success' => '1'];
    }
    
    public static function firstOrCreate($args){
        $r = self::where('recommender_id', $args['recommender_id'])
            ->where('recommendee_id', $args['recommendee_id'])
            ->where('episode_id', $args['episode_id'])
            ->first();
        
        if (!$r){
            $r = new self;
            $r->recommender_id  = $args['recommender_id'];
            $r->recommendee_id  = $args['recommendee_id'];
            $r->episode_id      = $args['episode_id'];
            $r->action          = isset($args['action']) ? $args['action'] : null;
            $r->autoaction      = isset($args['autoaction']) ? $args['autoaction'] : null;
            $r->slug            = Recommendation::findSlug();
            $r->save();
        }
        
        return $r;
    }
    
    public function recommendee(){
        return $this->belongsTo('App\User', 'recommendee_id');
    }
    
    public function recommender(){
        return $this->belongsTo('App\User', 'recommender_id');
    }
    
    public function episode(){
        return $this->belongsTo('App\Episode');
    }
}