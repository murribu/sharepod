<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Show;

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
    
    public function apiListing($user_id = null){
        return Show::orderBy('name')->get();
    }
    
    public function search(){
        return view('shows.search', ['activelink' => 'shows']);
    }
    
    public function postNew(){
        if (Input::get('feed') != ""){
            $show = new Show;
            $show->feed = Input::get('feed');
            if (@$show->save()){
                $show->parseFeed();
                return $show->name.' was successfully added!';
            }else{
                throw new Exception('Feed already exists');
            }
        }else{
            throw new Exception('Empty RSS Feed URL');
        }
    }
}
