<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');

        // $this->middleware('subscribed');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function show()
    {
        if ($_SERVER['HTTPS'] != "on") { 
        	return redirect('https://'.env('APP_URL'));
        }
        return view('home', ['activelink' => 'home']);
    }
    
    public function redirectToHome()
    {
        return redirect('/');
    }
    
    public function getManifest(){
        $manifest = [
            'manifest_version'  => 1,
            'name'              => env('APP_NAME')."1",
            'version'           => '0.1',
            'short_name'        => env('APP_SHORT_NAME')."1",
            
            'start_url'         => '/shows',
            'display'           => 'standalone',
            'background_color'  => '#000',
            'description'       => env('APP_DESCRIPTION'),
            'icons' => [
                    [
                        'src'       => 'img/logo.png',
                        'sizes'     => '48x48',
                        'type'      => 'image/png'
                    ]
                ],
            'related_applications' => [
                    [
                        'platform'  => 'web'
                    ]
                ],
            'intent_filter' => [
                    
                ]
        ];
        
        return $manifest;
    }
}
