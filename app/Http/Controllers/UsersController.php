<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Exception;

use App\User;

class UsersController extends Controller
{
    public function getUser($slug){
        return view('user');
    }
    
    public function apiGetUser($slug){
        return User::where('slug', $slug)
            ->select('id', 'slug', 'name')
            ->first();
    }
}