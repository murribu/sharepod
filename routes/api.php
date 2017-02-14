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
    Route::post('/episodes/archive', 'EpisodesController@apiArchive');
    Route::post('/episodes/unarchive', 'EpisodesController@apiUnarchive');
    
    Route::post('/shows/like', 'ShowsController@apiLike');
    Route::post('/shows/unlike', 'ShowsController@apiUnlike');
    
    Route::get('/recent_recommendees', 'RecommendationsController@apiGetRecentRecommendees');
    Route::get('/recommendations/{slug}', 'RecommendationsController@apiGetRecommendation');
    Route::get('/recommendations_pending', 'RecommendationsController@apiGetRecommendationsPending');
    Route::get('/recommendations_accepted', 'RecommendationsController@apiGetRecommendationsAccepted');

    Route::post('/recommendations/accept', 'RecommendationsController@apiAcceptRecommendations');
    Route::post('/recommendations/reject', 'RecommendationsController@apiRejectRecommendations');
    Route::post('/recommendations/make_pending', 'RecommendationsController@apiMakeRecommendationsPending');
    
    Route::get('/connections', 'ConnectionsController@apiGetConnections');
    Route::post('/connections/approve', 'ConnectionsController@apiApprove');
    Route::post('/connections/block', 'ConnectionsController@apiBlock');
    Route::post('/connections/make_pending', 'ConnectionsController@apiMakePending');
    
    Route::get('/playlists', 'PlaylistController@apiGetPlaylists');
    Route::post('/playlists/{slug}/add_episode', 'PlaylistController@apiPostAddEpisode');
    Route::post('/playlists/{slug}/move_up', 'PlaylistController@apiPostMoveUp');
    Route::post('/playlists/{slug}/move_to_top', 'PlaylistController@apiPostMoveToTop');
    Route::post('/playlists/{slug}/move_down', 'PlaylistController@apiPostMoveDown');
    Route::post('/playlists/{slug}/move_to_bottom', 'PlaylistController@apiPostMoveToBottom');
    Route::post('/playlists/{slug}/remove', 'PlaylistController@apiPostRemove');
    
    Route::get('/archived_episodes', 'UsersController@apiGetUserArchivedEpisodes');
});

Route::get('/users/{slug}/episodes_liked', 'UsersController@apiGetUserEpisodesLiked');
Route::get('/users/{slug}/shows_liked', 'UsersController@apiGetUserShowsLiked');
Route::get('/users/{slug}/playlists', 'UsersController@apiGetUserPlaylists');
Route::get('/users/{slug}/connections', 'UsersController@apiGetUserConnections');
Route::get('/users/{slug}/recommendations_accepted', 'UsersController@apiGetUserRecommendationsAccepted');

