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
    
    public function getManifest(){
        $manifest = [
            'name'              => env('APP_NAME'),
            'short_name'        => env('APP_SHORT_NAME'),
            'start_url'         => '.',
            'display'           => 'standalone',
            'background_color'  => '#fff',
            'description'       => env('APP_DESCRIPTION'),
            'icons' => [
                    'src'       => 'img/logo.png',
                    'sizes'     => '48x48',
                    'type'      => 'image/png'
                ],
            'related_applications' => [
                    'platform'  => 'web'
                ]
        ];
        
        return $manifest;
    }
}
