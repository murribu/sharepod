<?php

namespace App\Providers;
use Auth;
use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;

use Laravel\Spark\Contracts\Repositories\UserRepository;
use Laravel\Spark\Events\Profile\ContactInformationUpdated;

use Jrean\UserVerification\Facades\UserVerification;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [
        'vendor' => 'Murribu, inc',
        'product' => 'Sharepod',
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
        Spark::freePlan()
            ->features([
                'First', 'Second', 'Third'
            ]);

        Spark::plan('Basic', 'basic-1')
            ->price(10)
            ->features([
                'First', 'Second', 'Third'
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
