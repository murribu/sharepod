<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\Connection;

class ConnectionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('verified');
    }
    
    public function getConnections(){
        return view('connections', ['activelink' => 'connections']);
    }
    
    public function apiGetConnections(){
        return Connection::where('recommendee_id', Auth::user()->id)->get();
    }
}