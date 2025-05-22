<?php

use App\Http\Controllers\AmicizieController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpotifyApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UtentiController;

Route::middleware(['web'])->get('/auth/api-token', [AuthController::class, 'getToken']);

// logout
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logout effettuato']);
});


// API protette da Sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/spotify/renew', [SpotifyApiController::class, 'renewData', true]);
    Route::get('/spotify/top-tracks', [SpotifyApiController::class, 'topTracks']);
    Route::get('/spotify/top-artists', [SpotifyApiController::class, 'topArtists']);
    Route::get('/spotify/recently-played', [SpotifyApiController::class, 'recentlyPlayed']);
    Route::get('/spotify/top-genres', [SpotifyApiController::class, 'topGenres']);
    
    Route::get('/friend-request/send/{spotify_id}', [AmicizieController::class, 'sendRequest'])->name('friend-request.send');
    Route::get('/friend-request/accept/{spotify_id}', [AmicizieController::class, 'acceptRequest'])->name('friend-request.accept');
    Route::get('/friend-request/reject/{spotify_id}', [AmicizieController::class,'rejectRequest'])->name('friend-request.reject');
    Route::get('/friend-request/cancel/{spotify_id}', [AmicizieController::class,'cancelRequest'])->name('friend-request.cancel');
    Route::get('friend/remove/{spotify_id}', [AmicizieController::class,'removeFriend'])->name('friend-request.remove');
    Route::get('/friends/list', [AmicizieController::class, 'listFriends'])->name('friend-request.cancel');
    Route::get('/friend-requests/pending', [AmicizieController::class, 'pendingRequests'])->name('friend-request.cancel');
    Route::get('/user/search/{spotify_id}', [UtentiController::class, 'searchUserBySpotifyId'])->name('search.user.spotify_id');


    Route::post('/posts/create', [PostController::class, 'createPost'])->name('post.create');
    Route::get('/posts/{id}/react/{reaction_type}', [PostController::class, 'reactToPost'])->name('post.like');
    Route::get('/posts/feed', [PostController::class, 'feed'])->name('post.feed');
    Route::get('/post/{id}/MyReaction', [PostController::class, 'getMyReaction'])->name('post.myreaction');

    Route::get('/users/list/{username}', [AmicizieController::class, 'listByName']);


});


