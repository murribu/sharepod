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
        return Auth::user()->connections();
    }
    
    public function apiApprove(){
        if (Input::has('connection_id')){
            $c = Connection::where('id', Input::get('connection_id'))
                ->where('user_id', Auth::user()->id)
                ->first();
            if ($c){
                $c->status = 'approved';
                $c->save();
            }else{
                return $response->json('Connection not found or it doesn\'t belong to you.', 403);
            }
        }else{
            return $response->json('Must provide a connection_id', 400);
        }
    }
    
    public function apiBlock(){
        if (Input::has('connection_id')){
            $c = Connection::where('id', Input::get('connection_id'))
                ->where('user_id', Auth::user()->id)
                ->first();
            if ($c){
                $c->status = 'blocked';
                $c->save();
            }else{
                return $response->json('Connection not found or it doesn\'t belong to you.', 403);
            }
        }else{
            return $response->json('Must provide a connection_id', 400);
        }
    }
    
    public function apiMakePending(){
        if (Input::has('connection_id')){
            $c = Connection::where('id', Input::get('connection_id'))
                ->where('user_id', Auth::user()->id)
                ->first();
            if ($c){
                $c->status = null;
                $c->save();
            }else{
                return $response->json('Connection not found or it doesn\'t belong to you.', 403);
            }
        }else{
            return $response->json('Must provide a connection_id', 400);
        }
    }
}