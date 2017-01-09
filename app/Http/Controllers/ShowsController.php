<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Episode;
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
    
    public function search(){
        return view('shows.search', ['activelink' => 'shows']);
    }
    
    public function display($slug){
        $show = Show::where('slug', $slug)->first();
        if ($show){
            $activelink = 'shows';
            return view('show', compact('show', 'activelink'));
        }else{
            redirect('/');
        }
    }
    
    public function postNew(){
        if (Input::get('feed') != ""){
            $show = Show::where('feed', Input::get('feed'))->first();
            if (!$show){
                $show = new Show;
                $show->feed = Input::get('feed');
            }
            $show->save();
            $show->parseFeed();
            return $show->name.' was successfully added!';
        }else{
            throw new Exception('Empty RSS Feed URL');
        }
    }
    
    
    
    public function apiListing($user_id = null){
        return Show::orderBy('name')->get();
    }
    
    public function apiShow($slug){
        $show = Show::where('slug', $slug)
            ->where('active', 1)
            ->select('id', 'name', 'slug', 'description', 'img_url', 'url')
            ->first();
            
        $show->episodeCount = $show->episodes->count();
        $show->likesCount = $show->likesCount();
        $episodes = $show->limitedEpisodes(Auth::user(), 10);
        
        unset($show->id);
        unset($show->episodes);
        $show->episodes = $episodes;
        
        return $show;
    }
    
    public function apiShowEpisodes($slug){
        $show = Show::where('slug', $slug)
            ->where('active', 1);
        $user = Auth::user();
        if ($user){
            $show = $show->leftJoin('likes', function($join){
                $join->on('likes.user_id', '=', DB::raw($user->id));
                $join->on('likes.fk', '=', 'show.id');
                $join->on('likes.type', '=', 'show');
            });
        }
        $show->first();
        if ($show){
            return $show->limitedEpisodes(Auth::user(), 10, Input::get('pubdate'));
        }else{
            return [];
        }
    }
}
