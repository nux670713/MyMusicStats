<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Utenti;
use Illuminate\Support\Str;
use App\Models\BraniRecenti;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Spotify\SpotifyExtendSocialite;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Post;

// Benvenuto
Route::get('/', function () {
    return view('benvenuto');
});


// Dashboard protetta da login
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth:sanctum');

Route::post('/tokens/create', function (Request $request) {

    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];

});

//pagina crea post
Route::get('/dashboard/create-post', [PostController::class, 'index'])->name("crea-post");

// ---------------------------
// ROTTE SPOTIFY
// ---------------------------

Route::get('/auth/spotify/redirect', [AuthController::class, 'redirectToSpotify'])->name('spotify.redirect');

Route::get('auth/spotify/callback', function () {
    $user = Socialite::driver('spotify')->user();

    // Verifica se l'utente esiste già nel DB, altrimenti crealo
    $spotifyUser = Utenti::updateOrCreate(
        ['spotify_id' => $user->getId()],
        [
            'username' => $user->getName(),
            'email' => $user->getEmail(),
            'profile_picture' => $user->images[1]->url ?? asset('assets/img/default_profile.png'),
            'access_token' => $user->token,
            'refresh_token' => $user->refreshToken,
            'token_expires_at' => now()->addSeconds($user->expiresIn)
        ]
    );

    // Autenticazione dell'utente
    Auth::login($spotifyUser);

    return redirect()->to('/dashboard');
});

Route::get('/auth/spotify/redirect2', function () {
    return redirect()->route('spotify.redirect');
})->name('login');





