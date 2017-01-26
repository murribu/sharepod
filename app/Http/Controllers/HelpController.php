<?php namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Podcatcher;
use App\PodcatcherPlatform;

class HelpController extends Controller
{
    public function index(){
        $activelink = 'help';
        $podcatchers = Podcatcher::all();
        $platforms   = PodcatcherPlatform::select('platform')->distinct()->get();
        foreach($platforms as $p){
            $p->podcatchers = Podcatcher::whereIn('id', function($query) use ($p){
                    $query->select('podcatcher_id')
                        ->from('podcatcher_platforms')
                        ->where('platform', $p->platform);
                })->get();
        }
        return view('help', compact('podcatchers', 'platforms', 'activelink'));
    }
}