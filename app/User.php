<?php

namespace App;

use Laravel\Spark\User as SparkUser;

class User extends SparkUser
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
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
        $email = $facebook_user->email;
        if ($email == ''){
            $email = $facebook_user->id.'@facebook';
        }
        
        $user = User::where('email', $email)->first();
        if (!$user){
            $user = new User;
            $user->name = $facebook_user->name;
            $user->email = $email;

            $user->save();
        }

        return $user;
    }
    
    public function link_to_facebook($facebook_user, $input){
        $fb = $this->facebook_user();
        if (!$fb){
            $fb = new SocialUser;
            $fb->slug = SocialUser::findSlug();
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
        $fb->save();
        
        $this->facebook_user_id = $fb->id;
        $this->save();
        
        return $fb;
    }
    
    public static function first_or_create_from_twitter($twitter_user){
        $email = $twitter_user->id.'@twitter';
        
        $user = User::where('email', $email)->first();
        if (!$user){
            $user = new User;
            $user->name = $twitter_user->name;
            $user->email = $email;

            $user->save();
        }

        return $user;
    }
    
    public function link_to_twitter($twitter_user, $input){
        $twit = $this->twitter_user();
        if (!$twit){
            $twit = new SocialUser;
            $twit->slug = SocialUser::findSlug();
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
}
