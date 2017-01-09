<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the API routes for your application as
| the routes are automatically authenticated using the API guard and
| loaded automatically by this application's RouteServiceProvider.
|
*/

Route::group(['middleware' => 'auth:api'], function () {

    Route::post('episodes/like', 'EpisodesController@apiLike');
    Route::post('episodes/unlike', 'EpisodesController@apiUnlike');
    
    Route::get('shows', 'ShowsController@apiListing');
    Route::get('shows/{slug}', 'ShowsController@apiShow');
    Route::get('shows/{slug}/episodes', 'ShowsController@apiShowEpisodes');

});
