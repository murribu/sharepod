<?php namespace App\Http\Controllers;

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
        $episodes = $show->limitedEpisodes(10);
        foreach($episodes as $e){
            $e->howLongAgo = self::howLongAgo($e->pubdate);
            $e->pubdate_str = date('g:i A - j M Y', $e->pubdate);
            $e->description = strip_tags($e->description,"<p></p>");
        }
        
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
            $episodes = $show->limitedEpisodes(10, Input::get('pubdate'));
            foreach($episodes as $e){
                $e->howLongAgo = self::howLongAgo($e->pubdate);
                $e->pubdate_str = date('g:i A - j M Y', $e->pubdate);
                $e->description = strip_tags($e->description,"<p></p>");
            }
            
            return $episodes;
        }else{
            return [];
        }
    }
    
    public static function howLongAgo($pubdate){
        $short = false;
        $etime = time() - $pubdate;

        if ($etime < 1)      {
            return '0s';
        }

        $a = array( 365 * 24 * 60 * 60  =>  'year',
                   30 * 24 * 60 * 60  =>  'month',
                        24 * 60 * 60  =>  'day',
                             60 * 60  =>  'hour',
                                  60  =>  'minute',
                                   1  =>  'second'
                  );
        $a_units = array( 'year'   => array('short' => 'y', 'long' => 'year', 'longplural' => 'years'),
                         'month'  => array('short' => 'm', 'long' => 'month', 'longplural' => 'months'),
                         'day'    => array('short' => 'd', 'long' => 'day', 'longplural' => 'days'),
                         'hour'   => array('short' => 'h', 'long' => 'hour', 'longplural' => 'hours'),
                         'minute' => array('short' => 'm', 'long' => 'minute', 'longplural' => 'minutes'),
                         'second' => array('short' => 's', 'long' => 'second', 'longplural' => 'seconds')
                  );

        foreach ($a as $secs => $str){
            $d = $etime / $secs;
            if ($d >= 1){
                $r = round($d);
                if ($short){
                    return $r.$a_units[$str]['short'];
                }else{
                    return $r.' '.$a_units[$str][$r > 1 ? 'longplural' : 'long'].' ago';
                }
            }
        }
    }
}
