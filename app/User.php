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
    
    public static function create_from_twitter($twitter_user){
        if (!$user){
            $user = new User;
            $user->name = $twitter_user->name;
            if ($twitter_user->email){
                $user->email = $twitter_user->email;
            }else{
                $user->email = '@'.$twitter_user->nickname;
            }
            $user->save();
        }
        return $user;
    }
    
    public function link_to_twitter($twitter_user){
        $twit = $this->twitter_user();
        if (!$twit){
            $twit = new SocialUser;
            $twit->slug = SocialUser::findSlug();
        }
        $twit->social_id = $twitter_user->id;
        $twit->screen_name = $twitter_user->username;
        $twit->description = $twitter_user->user->description;
        $twit->url = $twitter_user->user->url;
        $twit->utc_offset = $twitter_user->user->utc_offset;
        // $twit->
    }
}
