<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@show');
Route::get('/home', 'HomeController@redirectToHome');
Route::get('/shows', 'ShowsController@show');
Route::get('/shows/list', 'ShowsController@listing');
Route::get('/shows/search', 'ShowsController@search');

$router->group(['middleware' => 'dev'], function ($router) {
    Route::get('/shows/new', 'ShowsController@getNew');
    Route::post('/shows/new', 'ShowsController@postNew');
    
});