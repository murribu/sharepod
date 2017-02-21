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
        return view('home', ['activelink' => 'home']);
    }
    
    public function redirectToHome()
    {
        return redirect('/');
    }
    
    // public function getEmail(){
        // $data = [
            // 'to_name' => 'George',
            // 'from_name' => 'Ringo',
            // 'link' => 'https://podcast.dj'
        // ];
        // return view('emails.send_episode', $data);
    // }
    
    public function getManifest(){
        $manifest = [
            'manifest_version'  => 1,
            'name'              => env('APP_NAME'),
            'version'           => '0.1',
            'short_name'        => env('APP_SHORT_NAME'),
            
            'start_url'         => '/',
            'display'           => 'standalone',
            'background_color'  => '#f5f8fa',
            'description'       => env('APP_DESCRIPTION'),
            'icons' => [
                    [
                        'src'       => 'img/logo.png',
                        'sizes'     => '48x48',
                        'type'      => 'image/png'
                    ],
                    [
                        'src'       => 'img/logo_128x128.png',
                        'sizes'     => '128x128',
                        'type'      => 'image/png'
                    ],
                    [
                        'src'       => 'img/logo_256x256.png',
                        'sizes'     => '256x256',
                        'type'      => 'image/png'
                    ],
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
