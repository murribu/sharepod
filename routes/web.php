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

Route::get('/auth/twitter', 'Auth\AuthController@redirectToTwitter');
Route::get('/auth/twitter/callback', 'Auth\AuthController@handleTwitterCallback');
Route::get('/auth/twitter/unlink', 'Auth\AuthController@unlinkTwitter');
Route::get('/auth/facebook', 'Auth\AuthController@redirectToFacebook');
Route::get('/auth/facebook/callback', 'Auth\AuthController@handleFacebookCallback');
Route::get('/auth/facebook/unlink', 'Auth\AuthController@unlinkFacebook');

Route::get('/send_verification_email', 'Auth\AuthController@sendVerificationEmail');
Route::get('/email-verification/check/{token}', 'Auth\AuthController@getVerification')->name('email-verification.check');

Route::get('/', 'HomeController@show');
Route::get('/home', 'HomeController@redirectToHome');
Route::get('/shows', 'ShowsController@show');
Route::get('/shows/list', 'ShowsController@listing');
Route::get('/shows/search', 'ShowsController@search');
Route::get('/shows/{slug}', 'ShowsController@display');

$router->group(['middleware' => 'auth'], function ($router) {
    Route::post('/recommend', 'EpisodesController@recommend');
    Route::get('/recommendations/{slug}', 'RecommendationsController@getRecommendation');
    Route::get('/recommendations', 'RecommendationsController@getRecommendations');
    
    Route::get('/connections', 'ConnectionsController@getConnections');
    
});

Route::get('/users/{slug}', 'UsersController@getUser');



$router->group(['middleware' => 'dev'], function ($router) {
    Route::post('/shows/new', 'ShowsController@postNew');
});


// Route::get('sendhtmlemail/{email_address}/{to_name}/{subject}','MailController@html_email');

// Route::get('test', function(){
    // return $_ENV;
// });
