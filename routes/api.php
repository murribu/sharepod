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

    Route::post('/episodes/like', 'EpisodesController@apiLike');
    Route::post('/episodes/unlike', 'EpisodesController@apiUnlike');
    Route::post('/shows/like', 'ShowsController@apiLike');
    Route::post('/shows/unlike', 'ShowsController@apiUnlike');
    
    Route::get('/recent_recommendees', 'RecommendationsController@apiGetRecentRecommendees');
    Route::get('/recommendations/{slug}', 'RecommendationsController@apiGetRecommendation');
    Route::get('/recommendations_given', 'RecommendationsController@apiGetRecommendationsGiven');
    Route::get('/recommendations_given_count', 'RecommendationsController@apiGetRecommendationsGivenCount');
    Route::get('/recommendations_received', 'RecommendationsController@apiGetRecommendationsReceived');
    Route::get('/recommendations_received_count', 'RecommendationsController@apiGetRecommendationsReceivedCount');

    
});

Route::get('/shows', 'ShowsController@apiListing');
Route::get('/shows/{slug}', 'ShowsController@apiShow');
Route::get('/shows/{slug}/episodes', 'ShowsController@apiShowEpisodes');

Route::get('/users/{slug}', 'UsersController@apiGetUser');
