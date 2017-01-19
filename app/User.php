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
    
    public function feed(){
        $episodes = Episode::whereIn('id', function($query){
            $query->select('episode_id')
                ->from('recommendations')
                ->where('recommendee_id', $this->id)
                ->where('action', 'accepted');
            })
            ->orderBy('pubdate', 'desc')
            ->get();

		$output  = "<?xml version='1.0' encoding='UTF-8'?>\n";
		$output .= "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:cc=\"http://web.resource.org/cc/\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" xmlns:media=\"http://search.yahoo.com/mrss/\" xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\">\n";
		$output .= "<channel>\n";
		$output .= "<atom:link href=\"".env('APP_URL')."/feed/".$this->slug."\" rel=\"self\" type=\"application/rss+xml\"/>
				<title>Recommendations for ".$this->name."</title>
				<pubDate>".gmdate("D, d M Y G:i:s")." +0000</pubDate>
				<lastBuildDate>".gmdate("D, d M Y G:i:s")." +0000</lastBuildDate>
				<generator>".env('APP_NAME')."</generator>
				<link>".env('APP_URL')."</link>
				<language>en</language>
				<copyright><![CDATA[]]></copyright>
				<docs>".env('APP_URL')."</docs>
				<managingEditor>".env('MAILGUN_FROM_EMAIL_ADDRESS')."</managingEditor>
				<description><![CDATA[Recommendations for ".$this->name." from ".env('APP_NAME')."]]></description>
				<image>
						<url>".env('APP_URL')."/img/logo.jpg</url>
						<title>".env('APP_NAME')."</title>
						<link><![CDATA[".env('APP_URL')."]]></link>
				</image>
				<itunes:author>".$this->name."</itunes:author>
				<itunes:keywords></itunes:keywords>
				<itunes:image href=\"".env('APP_URL')."/img/logo.jpg\" />
				<itunes:explicit></itunes:explicit>
				<itunes:owner>
						<itunes:name><![CDATA[".$this->name."]]></itunes:name>
						<itunes:email></itunes:email>
				</itunes:owner>
				<itunes:summary><![CDATA[Create your own podcast playlists at ".env('APP_URL')."]]></itunes:summary>
				<itunes:subtitle></itunes:subtitle>";
		foreach($episodes as $e){
			$output .= "<item>\n";
			$output .= "<title>" . str_replace("&","&amp;",$e->name) . "</title>\n";
			$output .= "<pubDate>" . gmdate("D, d M Y G:i:s",strtotime($e->pubdate)) . " +0000</pubDate>\n";
			$output .= "<guid isPermaLink=\"false\"><![CDATA[" . $e->guid . "]]></guid>\n";
			$output .= "<link><![CDATA[".$e->link."]]></link>\n";
			$output .= "<itunes:image href='".$e->img_url."' />\n";
			$output .= "<description><![CDATA[<a href='".env('APP_URL')."/episodes/".$e->slug."'>View this episode on ".env('APP_NAME')."</a><br><br>".$e->description."]]></description>\n";
			$output .= "<enclosure length=\"".$e->filesize."\" type=\"audio/mpeg\" url=\"".$e->url."\" />\n";
			$output .= "<itunes:duration>".$e->duration."</itunes:duration>\n";
			$output .= "<itunes:explicit>".$e->explicit."</itunes:explicit>";
			$output .= "<itunes:subtitle><![CDATA[<a href='".env('APP_URL')."/episodes/".$e->slug."'>View this episode on ".env('APP_NAME')."</a><br><br>".$e->description."]]></itunes:subtitle>";
			$output .= "</item>\n";
		}

		$output .= "</channel>\n";
		$output .= "</rss>";
		return $output;
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
