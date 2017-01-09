<?php namespace App\Http\Controllers;

use Auth;
use DB;
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
        $user = Auth::user();
        $show = Show::leftJoin('likes as this_user_likes', function($join) use ($user){
                $join->on('this_user_likes.user_id', '=', DB::raw($user ? $user->id : DB::raw("-1")));
                $join->on('this_user_likes.fk', '=', 'shows.id');
                $join->on('this_user_likes.type', '=', DB::raw("'show'"));
            })
            ->where('slug', $slug)
            ->where('active', 1)
            ->selectRaw('shows.id, shows.name, shows.slug, shows.description, shows.img_url, shows.url, count(this_user_likes.id) this_user_likes')
            ->groupBy('shows.id')
            ->groupBy('shows.name')
            ->groupBy('shows.slug')
            ->groupBy('shows.description')
            ->groupBy('shows.img_url')
            ->groupBy('shows.url')
            ->first();
            
        $show->episodeCount = $show->episodes->count();
        $show->total_likes = $show->likeCount();
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
    
    public function apiLike(){
        $user = Auth::user();
        if ($user){
            $s = Show::where('slug', Input::get('slug'))->first();
            if ($s->like($user)){
                return ['success' => 1, 'total_likes' => $s->likeCount(), 'this_user_likes' => 1];
            }
        }
    }
    
    public function apiUnlike(){
        $user = Auth::user();
        if ($user){
            $s = Show::where('slug', Input::get('slug'))->first();
            if ($s->unlike($user)){
                return ['success' => 1, 'total_likes' => $s->likeCount(), 'this_user_likes' => 0];
            }
        }
    }
}
