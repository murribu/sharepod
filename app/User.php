<?php
namespace App;

use DB;
use Laravel\Spark\User as SparkUser;

class User extends SparkUser
{
    use HasSlug;
    use HasFeed;
    
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
    
    public function playlists(){
        return $this->hasMany('App\Playlist');
    }
    
    public function can_add_a_playlist(){
        $plan = $this->plan();
        $playlist_count = $this->playlists->count();
        if ($playlist_count < intval(env('PLAN_FREE_PLAYLIST_COUNT'))){
            return true;
        }else{
            return $plan && 
                (
                    ($plan == env('PLAN_BASIC_NAME') && $playlist_count < intval(env('PLAN_BASIC_PLAYLIST_COUNT')))
                || 
                    ($plan == env('PLAN_PREMIUM_NAME') && $playlist_count < intval(env('PLAN_PREMIUM_PLAYLIST_COUNT')))
                );
        }
    }
    
    public function plan(){
        $sub = Subscription::where('user_id', $this->id)
            ->where(function($query){
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', DB::raw("now()"));
            })
            ->orderBy('ends_at', 'desc')
            ->first();
        if (count($sub) == 0){
            return false;
        }else{
            return $sub['stripe_plan'];
        }
    }
    
    public function info_for_feed(){
        $ret = [];
        $ret['episodes'] = Episode::whereIn('id', function($query){
            $query->select('episode_id')
                ->from('recommendations')
                ->where('recommendee_id', $this->id)
                ->where('action', 'accepted');
            })
            ->orderBy('pubdate', 'desc')
            ->get();
        $ret['url'] = env('APP_URL')."/feed/".$this->slug;
        $ret['name'] = $this->name;
        
        return $ret;
    }
    
    public function add_info(){
        $moreinfo = DB::select('select count(received.id) recommendations_received, count(given.id) recommendations_given, count(accepted.id) recommendations_accepted, count(acted_upon.id) recommendations_acted_upon, count(likes.id) likes, count(hitcounts.id) hitcounts
        from users
        left join likes on likes.user_id = users.id
        left join recommendations as received on received.recommendee_id = users.id
        left join recommendations as given on given.recommender_id = users.id
        left join recommendations as accepted on accepted.recommendee_id = users.id and accepted.action  = \'accepted\'
        left join recommendations as acted_upon on acted_upon.recommendee_id = users.id and (acted_upon.action is null or acted_upon.action = \'viewed\')
        left join hitcounts on hitcounts.user_id = users.id and hitcounts.request = \'user_feed\'
        where users.id = ?
        limit 1
        ', [$this->id]);
        
        if ($moreinfo[0]){
            $moreinfo = $moreinfo[0];
            $this->hasLikedSomething = $moreinfo->likes > 0 ? '1' : 0;
            $this->hasRecommendedSomething = $moreinfo->recommendations_given > 0 ? '1' : 0;
            $this->hasReceivedARecommendation = $moreinfo->recommendations_received > 0 ? '1' : 0;
            $this->hasAcceptedARecommendation = $moreinfo->recommendations_accepted > 0 ? '1' : 0;
            $this->hasTakenActionOnARecommendation = $moreinfo->recommendations_acted_upon > 0 ? '1' : 0;
            $this->hasRegisteredTheirFeed = $moreinfo->hitcounts > 0 ? '1' : 0;
        }
        return $this;
    }
    
    public function connections(){
        $db_received    = Connection::where('user_id', $this->id)->get();
        $db_given       = Connection::where('recommender_id', $this->id)->get();
        $received = [];
        $given = [];
        foreach ($db_received as $r){
            $received[] = [
                'connection_id' => $r->id,
                'user_name' => $r->user->name,
                'user_slug' => $r->user->slug,
                'recommender_name' => $r->recommender->name,
                'recommender_slug' => $r->recommender->slug,
                'status' => $r->status,
                'updated_at' => strtotime($r->updated_at),
            ];
        }
        foreach ($db_given as $g){
            $given[] = [
                'connection_id' => $g->id,
                'user_name' => $g->user->name,
                'user_slug' => $g->user->slug,
                'recommender_name' => $g->recommender->name,
                'recommender_slug' => $g->recommender->slug,
                'status' => $g->status,
                'updated_at' => strtotime($g->updated_at),
            ];
        }
        
        return compact('received', 'given');
    }
    
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
                    $action = 'accepted';
                    $autoaction = '1';
                    break;
                case 'blocked':
                    $action = 'rejected';
                    $autoaction = '1';
                    break;
                default:
                    //null
                    $action = null;
                    $autoaction = '0';
                    break;
            }
            $recommendation = Recommendation::firstOrCreate([
                    'recommender_id'    => $this->id,
                    'recommendee_id'    => $recommendee->id,
                    'episode_id'        => $ep->id,
                    'action'            => $action,
                    'autoaction'        => $autoaction
                ]);
            
            if ($action == null){
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
        return DB::select('select name, slug from users inner join (select distinct recommendee_id from recommendations where recommender_id = ? order by id desc limit 5) r on r.recommendee_id = users.id', [$this->id]);
    }
    
    public function recommendations_by_action($action = 'pending'){
        switch ($action){
            case 'pending':
                $action_clause = '(r.action is null or r.action = \'viewed\')';
                break;
            case 'accepted':
                $action_clause = 'r.action = \'accepted\'';
                break;
            case 'rejected':
                $action_clause = 'r.action = \'rejected\'';
                break;
            default:
                return ['error' => 'Invalid recommendation action'];
                break;
        }
        $episodes = DB::select('select e.slug, e.name, s.slug show_slug, s.name show_name, u.slug user_slug, u.name user_name, r.slug recommendation_slug from episodes e inner join recommendations r on r.episode_id = e.id left join users u on u.id = r.recommender_id left join shows s on s.id = e.show_id where r.recommendee_id = ? and '.$action_clause, [$this->id]);
        $ret = [];
        $e_slug = '';
        $ret_index = 0;
        foreach($episodes as $key=>$e){
            if ($e->slug == $e_slug){
                $ret[$ret_index - 1]['users'][] = ['name' => $e->user_name, 'slug' => $e->user_slug];
            }else{
                $ret[$ret_index++] = [
                    'name' => $e->name,
                    'slug' => $e->slug,
                    'show_name' => $e->show_name,
                    'show_slug' => $e->show_slug,
                    'users' => [[
                        'name' => $e->user_name,
                        'slug' => $e->user_slug,
                        'recommendation_slug' => $e->recommendation_slug
                    ]]
                ];
            }
        }
        return $ret;
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
