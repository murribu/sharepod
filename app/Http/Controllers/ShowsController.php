<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShowsController extends Controller
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
        return view('shows', ['activelink' => 'shows']);
    }
    
    public function listing(){
        return view('shows.list', ['activelink' => 'shows']);
    }
    
    public function search(){
        return view('shows.search', ['activelink' => 'shows']);
    }
}
