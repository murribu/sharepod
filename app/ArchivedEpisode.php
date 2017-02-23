<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Spark\Contracts\Repositories\NotificationRepository;
use Carbon\Carbon;

class ArchivedEpisode extends Model {
    use HasSlug;
    
    public $table = 'archived_episodes';
    
    public static function archive_one_episode(NotificationRepository $notifications){
        //meant to be called from a cronjob
        $lockfile = "/tmp/episode_archive.lock";

        if(!file_exists($lockfile))
            $fh = fopen($lockfile, "w");
        else
            $fh = fopen($lockfile, "r");

        if($fh === FALSE) exit("Unable to open lock file");

        if(!flock($fh, LOCK_EX)) // another process is running
            exit("Lock file already in use");
            
        $ae = ArchivedEpisode::where('active', '1')
            ->whereNull('result_slug')
            ->whereNotNull('episode_id')
            ->first();
        if ($ae){
            $notifications_to_send = [];
            $parts = split('.', $ae->episode->url);
            $ext = $parts[count($parts) - 1];
            $local_location = '/tmp/'.$this->slug.".".$ext;
            $s3_location = $this->slug.".".$ext;
            $out = fopen($local_location, "wb");
            if (!$out){ 
                $ae->result_slug = 'dj-local-file-storage-problem';
                $ae->processed_at = Carbon::now();
                $ae->save();
                foreach($ae->archived_episode_users as $aeu){
                    $aeu->active = 0;
                    $aeu->save();
                    $notifications_to_send[] = [
                        'user_id'       => $aeu->user_id,
                        'notification'  => [
                            'icon'          => 'fa-times',
                            'body'          => 'The episode has not been archived We had a problem.',
                            'action_text'   => 'View Episode',
                            'action_url'    => '/episodes/'.$ae->episode->slug,
                        ]
                    ];
                }
                $ret = ['error' => 'Local file could not be created in the /tmp folder'];
            }else{
                ini_set("memory_limit", "2048M");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->url);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_FILE, $out); 
                curl_exec($ch);
                if (curl_errno($ch)){
                    flock($fh, LOCK_UN);
                    return ['error' => 1, 'message' => 'Curl Error: '.curl_errno($ch), 'status_code' => curl_errno($ch)];
                }
                $needs_to_be_archived = false;
                foreach($ae->archived_episode_users as $aeu){
                    // Check to see if the user has enough space left on their plan
                    $limit = 0;
                    $plan = $aeu->user->plan();
                    if ($plan && $plan == env('PLAN_BASIC_NAME')){
                        $limit = intval(env('PLAN_BASIC_STORAGE_LIMIT'));
                    }
                    if ($plan && $plan == env('PLAN_PREMIUM_NAME')){
                        $limit = intval(env('PLAN_PREMIUM_STORAGE_LIMIT'));
                    }
                    $ae->filesize = File::size($local_location);
                    if ($user->storage() + File::size($local_location) > $limit){
                        $ae->result_slug = 'dj-storage-limit-exceeded';
                        $ae->message = 'The user has reached their storage limit';
                        $ae->save();
                        // return ['error' => 1, 'message' => 'You have reached your storage limit'];
                    }else{
                        
                    }
                }
            }
        }
        flock($fh, LOCK_UN);
        
        foreach($notifications_to_send as $n){
            $notifications->create($n['user_id'], $n['notification']);
        }
        
        return $ret;
    }
    
    public function users(){
        $self = $this;
        return User::whereIn('id', function($query) use ($self){
                $query->select('user_id')
                    ->from('archived_episode_requests')
                    ->where('archived_episode_id', $self->id)
                    ->where('status', 'success');
            })->get();
    }
    
    public function create_archived_episode_user($user){
        $aeu = ArchivedEpisodeUser::firstOrCreate(['archived_episode_id' => $this->id, 'user_id' => $user->id]);
        $aeu->active = 1;
        $aeu->save();
    }
    
    public function episode(){
        return $this->belongsTo('App\Episode');
    }
    
    public function archived_episode_users(){
        return $this->hasMany('App\ArchivedUserEpisode');
    }
}