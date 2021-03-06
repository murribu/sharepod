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
Route::get('/shows/{slug}/feed', 'ShowsController@getFeed');

Route::get('/episodes/{slug}', 'EpisodesController@getEpisode');

Route::get('/recommendations/{slug}', 'RecommendationsController@getRecommendation');

Route::get('/help', 'HelpController@index');

Route::get('/manifest.json', 'HomeController@getManifest');


$router->group(['middleware' => 'auth'], function ($router) {
    Route::post('/recommend', 'EpisodesController@recommend');
    Route::get('/recommendations', 'RecommendationsController@getRecommendations');
    
    Route::get('/connections', 'ConnectionsController@getConnections');
    
    Route::get('/playlists/new', 'PlaylistController@getEdit');
    Route::post('/playlists/new', 'PlaylistController@postEdit');
    Route::get('/playlists/{slug}/edit', 'PlaylistController@getEdit');
    Route::post('/playlists/{slug}/edit', 'PlaylistController@postEdit');
    
    Route::get('/me', 'UsersController@getMe');
    
    
});

Route::get('/playlists', 'PlaylistController@getPlaylists');
Route::get('/playlists/{slug}', 'PlaylistController@getPlaylist');
Route::get('/playlists/{slug}/feed', 'PlaylistController@getFeed');

Route::get('/users/{slug}', 'UsersController@getUser');
Route::get('/feed/{slug}', 'UsersController@getFeed');


$router->group(['middleware' => 'dev'], function ($router) {
    Route::post('/shows/new', 'ShowsController@postNew');
});

// Route::get('/email', 'HomeController@getEmail');



/** API endpoints for which I want to allow anonymous access, but use Auth::user(), if present **/

Route::get('/api/shows/{slug}/episodes', 'ShowsController@apiShowEpisodes');
Route::get('/api/shows', 'ShowsController@apiListing');
Route::get('/api/shows/search', 'ShowsController@apiSearch');
Route::get('/api/shows/{slug}', 'ShowsController@apiShow');
Route::get('/api/shows/{slug}/search', 'ShowsController@apiShowSearch');
Route::get('/api/episodes/popular', 'EpisodesController@apiGetPopular');
Route::get('/api/episodes/{slug}', 'EpisodesController@apiGetEpisode');
Route::get('/api/episodes/{slug}/likers', 'EpisodesController@apiGetEpisodeLikers');
Route::get('/api/users/{slug}', 'UsersController@apiGetUser');
Route::get('/api/playlists/popular', 'PlaylistController@apiGetPopularPlaylists');
Route::get('/api/playlists/{slug}', 'PlaylistController@apiGetPlaylist');

/**  **/

// Route::get('sendhtmlemail/{email_address}/{to_name}/{subject}','MailController@html_email');

// Route::get('test', function(){
    // return $_ENV;
// });
