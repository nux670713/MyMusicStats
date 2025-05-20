<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Utenti;
use App\Models\Amicizie;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\BraniRecenti;
use App\Models\Generi;
use App\Models\TopTrack;
use App\Models\TopArtist;

class MockUserSeeder extends Seeder
{
    public function run(): void
    {
        // Crea utente mock
        $utente = Utenti::create([
            'username' => 'mockuser',
            'email' => 'mock@example.com',
            'access_token' => 'mock_access_token',
            'refresh_token' => 'mock_refresh_token',
        ]);

        // Aggiungi amicizia
        $amico = Utenti::create([
            'username' => 'amico',
            'email' => 'amico@example.com',
        ]);

        Amicizie::create([
            'id_mittente' => $utente->id_utente,
            'id_destinatario' => $amico->id_utente,
            'status' => 'accepted',
        ]);

        // Post e Like
        $post1 = Post::create([
            'id_utente' => $utente->id_utente,
            'content' => 'Questo è un post di test!',
        ]);

        PostLike::create([
            'id_post' => $post1->id_post,
            'id_utente' => $amico->id_utente,
        ]);

        // Brani recenti
        BraniRecenti::create([
            'id_utente' => $utente->id_utente,
            'id_spotify' => 'spotify:track:123',
            'track_name' => 'Brano Mock',
            'artist' => 'Artista Mock',
            'album' => 'Album Mock',
            'genere' => 'Pop',
        ]);

        // Generi
        Generi::create([
            'id_utente' => $utente->id_utente,
            'genere' => 'Pop',
        ]);

        // Top Tracks
        TopTrack::create([
            'id_utente' => $utente->id_utente,
            'rank' => 1,
            'id_spotify' => 'spotify:track:456',
            'track_name' => 'Top Brano',
            'artist' => 'Top Artista',
            'album' => 'Top Album',
            'genere' => 'Pop',
        ]);

        // Top Artists
        TopArtist::create([
            'id_utente' => $utente->id_utente,
            'rank' => 1,
            'id_spotify' => 'spotify:artist:789',
            'artist' => 'Top Artista',
        ]);
    }
}
