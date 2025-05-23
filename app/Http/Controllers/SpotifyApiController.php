<?php

namespace App\Http\Controllers;

use App\Models\TopTrack;
use App\Models\TopArtist;
use App\Models\BraniRecenti;
use App\Models\Generi;
use App\Models\Utenti;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\TextUI\XmlConfiguration\RenameBeStrictAboutCoversAnnotationAttribute;

class SpotifyApiController extends Controller
{

    public function renewData($force = false)
    {
        //tronca tutti i dati dell'utente
        //se updated_at dello user è maggiore di 24 ore
        $user = Auth::user();
        $userId = $user->id_utente;
        $lastUpdate = Utenti::where('id_utente', $userId)->first();
        if (($lastUpdate && $lastUpdate->updated_at->diffInHours(now()) > 24) || $force) {
            DB::table('brani_recenti')->where('id_utente', $userId)->delete();
            DB::table('top_artists')->where('id_utente', $userId)->delete();
            DB::table('top_tracks')->where('id_utente', $userId)->delete();
            DB::table('generi')->where('id_utente', $userId)->delete();
        }

    }

    public function topTracks()
    {
        /**
         * @var Utenti $user
         */
        $user = Auth::user();

        // Parametri personalizzabili via GET
        $limit = request()->query('limit', 10); // default 10
        $timeRange = request()->query('time_range', 'short_term'); // default medium_term

        // Protezione: limiti massimi/minimi
        $limit = max(1, min($limit, 50)); // Spotify accetta da 1 a 50

        $response = [];

        $this->renewData();

        $topTracks = TopTrack::where('id_utente', '=', $user->id_utente)->where('time_range', '=', $timeRange)->orderBy('rank', 'asc')->take($limit)->get();
        if ($topTracks->count() < 0) {
            $response = [
                'items' => $topTracks->map(function ($track) {
                    return [
                        'album' => [
                            'album_type' => 'album',
                            'artists' => [
                                [
                                    'name' => $track->artist,
                                    'type' => 'artist',
                                ],
                            ],

                            'images' => [
                                [
                                    'height' => 300,
                                    'url' => $track->img_url,
                                    'width' => 300,
                                ],
                            ],
                        ],
                        'artists' => [
                            [
                                'name' => $track->artist,
                                'type' => 'artist',
                            ],
                        ],

                        'name' => $track->track_name,
                    ];
                }),
            ];
            return response()->json($response);
        }

        $response = Http::withToken($user->getValidAccessToken())
            ->get('https://api.spotify.com/v1/me/top/tracks', [
                'limit' => $limit,
                'time_range' => $timeRange,
            ])
            ->json();

        if (!isset($response['items'])) {
            return response()->json(['error' => 'Errore nella risposta di Spotify'], 500);
        }
        // Salva i dati nel database
        $i = 0;
        foreach ($response['items'] as $item) {
            TopTrack::create([
                'id_utente' => $user->id_utente,
                'id_spotify' => $item['id'],
                'track_name' => $item['name'],
                'rank' => $i,
                'artist' => $item['artists'][0]['name'] ?? null,
                'img_url' => $item['album']['images'][0]['url'] ?? null,
                'time_range' => $timeRange,
            ]);
            $i++;
        }

        // aggiorno l'updated_at dell'utente
        $user->updated_at = now();
        $user->save();

        return response()->json($response);
    }

    public function topArtists()
    {
        /**
         * @var Utenti $user
         */
        $user = Auth::user();

        $limit = request()->query('limit', 10);
        $timeRange = request()->query('time_range', 'short_term');

        $limit = max(1, min($limit, 50));

        $this->renewData();


        $topArtists = TopArtist::where('id_utente', '=', $user->id_utente)->where('time_range', '=', $timeRange)->orderBy('rank', 'asc')->take($limit)->get();
        if ($topArtists->count() > 0) {
            $response = [
                'items' => $topArtists->map(function ($artist) {
                    return [
                        'id' => $artist->id_spotify,
                        'images' => [
                            [
                                'url' => $artist->img_url,
                                'height' => 300,
                                'width' => 300,
                            ],
                            [
                                'url' => $artist->img_url,
                                'height' => 300,
                                'width' => 300,
                            ],
                        ],
                        'name' => $artist->artist,
                        'type' => 'artist',
                    ];
                }),
            ];
            return response()->json($response);
        }

        // Chiamata API a Spotify
        $response = Http::withToken($user->getValidAccessToken())
            ->get('https://api.spotify.com/v1/me/top/artists', [
                'limit' => $limit,
                'time_range' => $timeRange,
            ])
            ->json();

        if (!isset($response['items'])) {
            return response()->json(['error' => 'Errore nella risposta di Spotify'], 500);
        }

        // Salva i dati nel database
        $i = 0;
        foreach ($response['items'] as $artist) {
            TopArtist::create([
                'id_utente' => $user->id_utente,
                'id_spotify' => $artist['id'],
                'artist' => $artist['name'],
                'rank' => $i,
                'img_url' => $artist['images'][0]['url'] ?? null,
                'time_range' => $timeRange,
            ]);
            $i++;
        }

        // aggiorno l'updated_at dell'utente
        $user->updated_at = now();
        $user->save();

        return response()->json($response);
    }

    public function topGenres()
    {
        /** @var \App\Models\Utenti $user */
        $user = Auth::user();

        // Recupera parametri GET con valori di default
        $limit = request()->query('limit', 25);
        $timeRange = request()->query('time_range', 'short_term');

        $limit = max(1, min($limit, 25));

        $response = [];

        $this->renewData();


        // Controlla se ci sono dati nel database per questa sessione
        $topGenres = Generi::where('id_utente', '=', $user->id_utente)->where('time_range', '=', $timeRange)->orderBy('created_at', 'desc')->take($limit)->get();
        if ($topGenres->isNotEmpty()) {
            // Se ci sono dati nel database, restituisci quelli
            $response = $topGenres->pluck('count', 'genere');

            return response()->json($response);
        }

        // Chiamata API a Spotify
        $response = Http::withToken($user->getValidAccessToken())->get('https://api.spotify.com/v1/me/top/artists', [
            'limit' => $limit,
            'time_range' => $timeRange,
        ]);

        // aggiorno l'updated_at dell'utente
        $user->updated_at = now();
        $user->save();

        $artists = $response->json();

        $genresCount = [];

        if (isset($artists['items']) && !empty($artists['items'])) {
            foreach ($artists['items'] as $artist) {
                if (!empty($artist['genres'])) {
                    foreach ($artist['genres'] as $genre) {
                        $genresCount[$genre] = ($genresCount[$genre] ?? 0) + 1;
                    }
                }
            }

            // Ordina i generi per frequenza (decrescente)
            arsort($genresCount);
            foreach ($genresCount as $genere => $count) {
                Generi::create([
                    'id_utente' => $user->id_utente,
                    'genere' => $genere,
                    'count' => $count,
                    'time_range' => $timeRange,
                ]);
                // Salva i generi nel database
            }

            return response()->json($genresCount); // Restituisci solo i generi
        }

        return response()->json([]);
    }

    public function recentlyPlayed()
    {
        /** @var \App\Models\Utenti $user */
        $user = Auth::user();
        $userId = $user->id_utente;

        // Parametri personalizzabili via GET
        $limit = request()->query('limit', 10); // default 10

        $this->renewData();


        // Controlla se ci sono dati nel database per questa sessione
        $recentTracks = BraniRecenti::where('id_utente', '=', $userId)->orderBy('created_at', 'desc')->take($limit)->get();

        if ($recentTracks->isNotEmpty()) {
            // Se ci sono dati nel database, restituisci quelli
            $response = [
                'items' => $recentTracks->map(function ($track) {
                    return [
                        'track' => [
                            'name' => $track->track_name,
                            'artists' => [['name' => $track->artist]],
                            'album' => [
                                'name' => $track->album,
                                'images' => [
                                    [
                                        'url' => $track->img_url,
                                        'height' => 300,
                                        'width' => 300,
                                    ],
                                ],
                            ],
                            'id' => $track->id_spotify,
                        ],
                        'played_at' => \Carbon\Carbon::parse($track->played_at)->format('Y-m-d\TH:i:s.v\Z'),
                    ];
                }),
            ];

            return response()->json($response);
        }

        // Altrimenti, recupera i dati da Spotify
        $response = Http::withToken($user->getValidAccessToken())
            ->get('https://api.spotify.com/v1/me/player/recently-played', [
                'limit' => $limit,
            ])
            ->json();

        if (!isset($response['items'])) {
            return response()->json(['error' => 'Errore nella risposta di Spotify'], 500);
        }

        // aggiorno l'updated_at dell'utente
        $user->updated_at = now();
        $user->save();

        // Salva i dati nel database
        foreach ($response['items'] as $item) {
            $track = $item['track'];
            BraniRecenti::create([
                'id_utente' => $userId,
                'id_spotify' => $track['id'],
                'track_name' => $track['name'],
                'artist' => $track['artists'][0]['name'] ?? null,
                'album' => $track['album']['name'] ?? null,
                'img_url' => $track['album']['images'][0]['url'] ?? null,
                'played_at' => $item['played_at'],
            ]);
        }

        return response()->json($response);
    }
}
