<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Utenti; 
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    // Redirect a Spotify
    public function redirectToSpotify()
    {
        /** @disregard P1009 Undefined method */
        return Socialite::driver('spotify')
            ->scopes(['user-read-email', 'user-read-private','user-top-read', 'user-read-recently-played'])    
        ->redirect()
            ;
    }

   
    public function getToken()
    {
        if (Auth::check()) {
            // L'utente è loggato, restituisci un token valido
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'auth_token' => $token,
            ]);
        }

        // L'utente non è loggato, reindirizzalo alla route di login
        return response()->json([
            'error' => 'Utente non autenticato',
        ], 401);
    }
}
