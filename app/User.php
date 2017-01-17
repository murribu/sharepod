<?php
namespace App;

use DB;
use Laravel\Spark\User as SparkUser;

class User extends SparkUser
{
    use HasSlug;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'verified',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
        'facebook_user_id',
        'twitter_user_id',
        'extra_billing_information',
        'verification_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'date',
        'uses_two_factor_auth' => 'boolean',
    ];
    
    public function recommend($input){
        $user = $this;
        $ep = Episode::where('slug', $input['slug'])->first();
        if ($ep){
            if (isset($input['user_slug'])){
                $recommendee = User::where('slug', $input['user_slug'])
                    ->first();
            }elseif (isset($input['email_address'])){
                $recommendee = User::where('email', $input['email_address'])
                    ->first();
                    
                if (!$recommendee){
                    $recommendee = new User;
                    $recommendee->name = $input['email_address'];
                    $recommendee->email = $input['email_address'];
                    $recommendee->slug = User::findSlug($input['email_address']);
                    $recommendee->password = bcrypt(User::findSlug()); //random string
                    $recommendee->verified = 0;
                    $recommendee->save();
                }
            }elseif (isset($input['twitter_handle'])){
                $recommendee = User::whereIn('twitter_user_id', function($query){
                    $query->select('id')
                        ->from('social_users')
                        ->where('screen_name', $input['twitter_handle'])
                        ->where('type', DB::raw('twitter'));
                    })->first();
                
                if (!$recommendee){
                    $social_user = new SocialUser;
                    $social_user->type = 'twitter';
                    $social_user->slug = SocialUser::findSlug();
                    $social_user->screen_name = $input['twitter_handle'];
                    $social_user->save();
                    
                    $recommendee = new User;
                    $recommendee->slug = User::findSlug($input['twitter_handle']);
                    $recommendee->name = $input['twitter_handle'];
                    $recommendee->twitter_user_id = $social_user->id;
                    $recommendee->password = bcrypt(User::findSlug()); //random string
                    $recommendee->verified = 0;
                    $recommendee->save();
                }
            }else{
                return ['message' => 'Must provide a User, Email Address, or Twitter Handle', 'error' => 400];
            }
            if (!$recommendee){
                return ['message' => 'User does not exist', 'error' => 404];
            }
            $connection = Connection::where('user_id', $recommendee->id)
                ->where('recommender_id', $this->id)
                ->first();
                
            if (!$connection){
                $connection = new Connection;
                $connection->user_id = $recommendee->id;
                $connection->recommender_id = $this->id;
                $connection->save();
            }
            switch ($connection->status){
                case 'approved':
                    $status = 'accepted';
                    break;
                case 'blocked':
                    $status = 'rejected';
                    break;
                default:
                    //null
                    $status = null;
                    break;
            }
            $recommendation = Recommendation::firstOrCreate([
                    'recommender_id'    => $this->id,
                    'recommendee_id'    => $recommendee->id,
                    'episode_id'        => $ep->id,
                    'action'            => $status
                ]);
            
            if ($status == null){
                if (isset($input['email_address'])){
                    $recommendation->send_via_email();
                }else if (isset($input['twitter_handle'])){
                    $recommendation->send_via_twitter();
                }
            }
            
            return $recommendation;
        }else{
            return ['message' => 'Episode does not exist', 'error' => 404];
        }
    }
    
    public function recent_recommendees(){
        $user = $this;
        return DB::select('select name, slug from users inner join (select distinct recommendee_id from recommendations where recommender_id = ? order by id desc limit 5) r on r.recommendee_id = users.id', [$this->id]);
    }
    
    public function twitter_user(){
        if ($this->twitter_user_id){
            return SocialUser::find($this->twitter_user_id);
        }
    }
    
    public function facebook_user(){
        if ($this->facebook_user_id){
            return SocialUser::find($this->facebook_user_id);
        }
    }
    
    public static function first_or_create_from_facebook($facebook_user){
        $id = $facebook_user->id;
        
        $fb = SocialUser::where('social_id', $id)->where('type', 'facebook')->first();
        $user = false;
        if ($fb){
            $user = $fb->user;
        }
        if (!$user){
            $user = new User;
            $user->slug = User::findSlug($facebook_user->name);
            $user->name = $facebook_user->name;
            $user->email = $facebook_user->email;
            $user->verified = 1;

            $user->save();
        }

        return $user;
    }
    
    public function link_to_facebook($facebook_user, $input){
        $fb = $this->facebook_user();
        if (!$fb){
            $fb = SocialUser::where('type', 'facebook')
                ->where('social_id', $facebook_user->id)
                ->first();
            if (!$fb){
                $fb = new SocialUser;
                $fb->slug = SocialUser::findSlug();
            }
        }
        
        $fb->name = $facebook_user->name;
        $fb->email = $facebook_user->email;
        $fb->social_id = $facebook_user->id;
        $fb->screen_name = $facebook_user->nickname;
        $fb->url = $facebook_user->profileUrl;
        $fb->avatar = $facebook_user->avatar;
        $fb->avatar_original = $facebook_user->avatar_original;
        $fb->token = $facebook_user->token;
        $fb->gender = $facebook_user->user && $facebook_user->user['gender'] ? substr($facebook_user->user['gender'],0,1) : null;
        $fb->code = $input['code'];
        $fb->state = $input['state'];
        $fb->type = 'facebook';
        $fb->save();
        
        if ($fb->email != null && $fb->email != ''){
            $this->email = $fb->email;
        }
        $this->facebook_user_id = $fb->id;
        $this->save();
        
        return $fb;
    }
    
    public static function first_or_create_from_twitter($twitter_user){
        $id = $twitter_user->id;
        
        $twit = SocialUser::where('social_id', $id)->where('type', 'twitter')->first();
        $user = false;
        if ($twit){
            $user = $twit->user;
        }
        if (!$user){
            $user = new User;
            $user->slug = User::findSlug($twitter_user->nickname);
            $user->name = $twitter_user->name;
            $user->email = $twitter_user->email;
            $user->verified = 1;

            $user->save();
        }

        return $user;
    }
    
    public function link_to_twitter($twitter_user, $input){
        $twit = $this->twitter_user();
        if (!$twit){
            if (!$twit){
                $twit = SocialUser::where('type', 'twitter')
                    ->where('social_id', $twitter_user->id)
                    ->first();
                if (!$twit){
                    $twit = new SocialUser;
                    $twit->slug = SocialUser::findSlug();
                }
            }
        }
        $twit->name = $twitter_user->name;
        $twit->social_id = $twitter_user->id;
        $twit->screen_name = $twitter_user->nickname;
        $twit->description = $twitter_user->user['description'];
        $twit->url = $twitter_user->user['url'];
        $twit->utc_offset = $twitter_user->user['utc_offset'];
        $twit->profile_background_image_url = $twitter_user->user['profile_background_image_url'];
        $twit->profile_image_url = $twitter_user->user['profile_image_url'];
        $twit->oauth_token = $input['oauth_token'];
        $twit->oauth_verifier = $input['oauth_verifier'];
        $twit->token = $twitter_user->token;
        $twit->token_secret = $twitter_user->tokenSecret;
        $twit->nickname = $twitter_user->nickname;
        $twit->email = $twitter_user->email;
        $twit->avatar = $twitter_user->avatar;
        $twit->avatar_original = $twitter_user->avatar_original;
        $twit->type = 'twitter';
        $twit->save();
        
        $this->twitter_user_id = $twit->id;
        $this->save();
        
        return $twit;
    }
    
    public static function first_or_create_to_send_via_email($email_address){
        $user = User::where('email', $email_address)->first();
        
        if (!$user){
            $user = new User;
            $user->email = $email_address;
            $user->slug = User::findSlug($email_address);
            $user->save();
        }
        
        return $user;
    }
}
