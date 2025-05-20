<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utenti;

class UtentiController extends Controller
{
    public function searchUserBySpotifyId(Request $request)
    {
        $spotifyId = $request->input('spotify_id');

        // Esegui la ricerca dell'utente in base all'ID Spotify
        $user = Utenti::where('spotify_id', $spotifyId)->first();

        if ($user) {
            return response()->json(['user' => $user], 200);
        } else {
            return response()->json(['message' => 'Utente non trovato.'], 404);
        }
    }
}
