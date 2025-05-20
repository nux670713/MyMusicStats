<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Utenti;
use App\Models\BraniRecenti;
use App\Models\TopArtist;
use App\Models\TopTrack;

class DashboardController extends Controller
{
    public function index()
    {
        // Recupera l'utente autenticato
        $utente = Auth::user();

        if (!$utente) {
            return redirect()->route('benvenuto')->with('error', 'Non sei autenticato!');
        }
        // Estrae i dati necessari
        $username = $utente->username;
        $pfp = $utente->profile_picture;

        return view('dashboard', compact('username', 'pfp'));
    }


}

