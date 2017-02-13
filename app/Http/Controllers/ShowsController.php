<?php namespace App\Http\Controllers;

use Auth;
use DB;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Episode;
use App\Hitcount;
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

        $this->middleware('verified')->only(['apiLike', 'apiUnlike']);
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
    
    public function apiSearch(){
        return DB::select('select name, slug, description from shows where active and (name like ? or description like ?) order by name limit 20', ['%'.Input::get('s').'%', '%'.Input::get('s').'%']);
    }
    
    public function apiListing(){
        $shows = [];
        
        $categories_db = Show::select('category')
            ->whereNotNull('category')
            ->where('active', '1')
            ->distinct()
            ->orderBy('category')->get();
        $categories = ['All'];
        foreach($categories_db as $category_db){
            $categories[] = $category_db->category;
        }
        
        $shows['All'] = Show::popular(Auth::user());
        foreach($categories as $cat){
            if ($cat != 'All'){
                $shows[$cat] = Show::popular(Auth::user(), $cat);
            }
            foreach($shows[$cat] as $show){
                $show = $show->prepare();
            }
        }
        
        return compact('categories', 'shows');
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
            ->selectRaw('shows.id, shows.name, shows.slug, shows.description, shows.img_url, shows.url, shows.feed, count(this_user_likes.id) this_user_likes')
            ->groupBy('shows.id')
            ->groupBy('shows.feed')
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
            ->where('active', 1)
            ->first();
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
    
    public function getFeed($slug){
        $show = Show::where('slug', $slug)->first();
        if ($show){
            $hitcount = new Hitcount;
            $hitcount->request = 'show_feed';
            $hitcount->user_id = Auth::user() ? Auth::user()->id : null;
            $hitcount->ip = \Request::getClientIp();
            $hitcount->fk = $show->id;
            $hitcount->save();
            
            return Response::make($show->feed(), 200)->header('Content-Type', 'application/xml');
        }else{
            return response()->json('Show not found', 404);
        }
    }
    
    public function apiGetShowCategories(){
        return Show::select('category')->distinct()->orderBy('category')->get();
    }
}
