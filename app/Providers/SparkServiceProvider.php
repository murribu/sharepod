<?php

namespace App\Providers;
use Auth;
use Carbon\Carbon;
use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;

use Laravel\Spark\Contracts\Repositories\UserRepository;
use Laravel\Spark\Events\Profile\ContactInformationUpdated;

use Jrean\UserVerification\Facades\UserVerification;

use App\User;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [
        'vendor' => 'Murribu, inc',
        'product' => 'PodcastDJ',
        'street' => '',
        'location' => 'Franklin, TN 37069',
        'phone' => '615-555-5555',
    ];

    /**
     * The address where customer support e-mails should be sent.
     *
     * @var string
     */
    protected $sendSupportEmailsTo = null;

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    protected $developers = [
        'murribu@gmail.com'
    ];

    /**
     * Indicates if the application will expose an API.
     *
     * @var bool
     */
    protected $usesApi = true;

    /**
     * Finish configuring Spark for the application.
     *
     * @return void
     */
    public function booted()
    {
        Spark::freePlan('Free', 'free')
            ->features([
                'Receive unlimited recommendations', 
                'Send up to '.env('PLAN_FREE_RECOMMENDATION_COUNT').' recommendations per 24 hours', 
                'Create up to '.env('PLAN_FREE_PLAYLIST_COUNT').' playlists'
            ]);

        Spark::plan('Basic', 'basic-1')
            ->price(10)
            ->features([
                'Send up to '.env('PLAN_BASIC_RECOMMENDATION_COUNT').' recommendations per 24 hours', 
                'Create up to '.env('PLAN_BASIC_PLAYLIST_COUNT').' playlists',
                'Archive episodes (up to '.(intval(env('PLAN_BASIC_STORAGE_LIMIT'))/pow(2,30)).' GB)',
            ]);
            
        Spark::plan('Premium', 'premium-1')
            ->price(20)
            ->features([
                'Send unlimited recommendations', 
                'Create unlimited playlists',
                'Archive episodes (up to '.(intval(env('PLAN_PREMIUM_STORAGE_LIMIT'))/pow(2,30)).' GB)',
                'Archive shows (Coming soon)',
                'Secure playlists with a username and password (Coming soon)'
            ]);
            
        Spark::swap('UpdateContactInformation@handle', function($user, array $data){
            $user->forceFill([
                'name' => $data['name'],
                'email' => $data['email'],
                'verified' => 0
            ])->save();
            
            UserVerification::generate($user);
            UserVerification::send($user, 'Verify your email for Shaarepod');

            event(new ContactInformationUpdated($user));

            return $user;
        });
        
        Spark::swap('Laravel\Spark\Contracts\Repositories\UserRepository@create', function(array $data){
            $user = Spark::user();
    
            $user->forceFill([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'last_read_announcements_at' => Carbon::now(),
                'trial_ends_at' => Carbon::now()->addDays(Spark::trialDays()),
                'slug' => User::findSlug($data['name']),
            ])->save();
    
            return $user;
        });
        
        Spark::swap('Laravel\Spark\Contracts\Repositories\UserRepository@current', function(){
            $user = null;
            if (Auth::check()) {
                $user = $this->find(Auth::id())->shouldHaveSelfVisibility();
                $user = $user->add_info();
            }
            return $user;
        });
        
    }
}
