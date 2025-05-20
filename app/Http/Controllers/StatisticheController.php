<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StatisticheController extends Controller
{
    private function getSpotifyHeaders()
    {
        $user = Auth::user();
        $accessToken = $user?->access_token;

        if (!$accessToken) {
            throw new \Exception("Token Spotify mancante");
        }

        return [
            'Authorization' => 'Bearer ' . $accessToken,
        ];
    }

    private function spotifyRequest($endpoint, $params = [])
    {
        try {
            $user = Auth::user();
            $accessToken = $user->getValidAccessToken(); // usa il tuo metodo per rigenerare il token se serve

            if (isset($params['time_range']) && !in_array($params['time_range'], ['short_term', 'medium_term', 'long_term'])) {
                $params['time_range'] = 'medium_term';
            }

            if (isset($params['limit'])) {
                $params['limit'] = min(max(1, (int) $params['limit']), 50);
            }

            $response = Http::withToken($accessToken)->get("https://api.spotify.com/v1/{$endpoint}", $params);

            if ($response->successful()) {
                return $response->json();
            }

            if ($response->status() === 401) {
                return ['error' => 'Token scaduto', 'status' => 401];
            }

            Log::error("Spotify API error: {$response->status()}", [
                'endpoint' => $endpoint,
                'params' => $params,
                'response' => $response->json()
            ]);

            return ['error' => 'Spotify API error: ' . $response->status(), 'status' => $response->status()];
        } catch (\Exception $e) {
            Log::error("Exception in Spotify request: {$e->getMessage()}");
            return ['error' => 'Errore di connessione', 'status' => 500];
        }
    }

    private function formatTrack($track)
    {
        return [
            'id' => $track['id'],
            'name' => $track['name'],
            'artists' => collect($track['artists'])->pluck('name')->toArray(),
            'album' => $track['album']['name'],
            'image' => $track['album']['images'][0]['url'] ?? null,
            'preview_url' => $track['preview_url'] ?? null,
            'external_url' => $track['external_urls']['spotify'] ?? null,
        ];
    }


    public function getTopTracks(Request $request)
    {
        $timeRange = $request->input('time_range', 'long_term');
        $limit = (int) $request->input('limit', 50);

        if (!in_array($timeRange, ['short_term', 'medium_term', 'long_term'])) {
            $timeRange = 'long_term';
        }

        $limit = min(max(1, $limit), 50);

        $data = $this->spotifyRequest('me/top/tracks', [
            'limit' => $limit,
            'time_range' => $timeRange,
        ]);

        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']], $data['status'] ?? 400);
        }

        $tracks = collect($data['items'] ?? [])->map(fn($track) => $this->formatTrack($track));

        return response()->json($tracks->values());
    }

    public function getTopArtists(Request $request)
    {
        $timeRange = $request->input('time_range', 'medium_term');
        $limit = (int) $request->input('limit', 20);

        if (!in_array($timeRange, ['short_term', 'medium_term', 'long_term'])) {
            $timeRange = 'medium_term';
        }

        $limit = min(max(1, $limit), 50);

        $data = $this->spotifyRequest('me/top/artists', [
            'limit' => $limit,
            'time_range' => $timeRange,
        ]);

        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']], $data['status'] ?? 400);
        }

        $artists = collect($data['items'] ?? [])->map(function ($artist) {
            return [
                'id' => $artist['id'],
                'name' => $artist['name'],
                'genres' => $artist['genres'] ?? [],
                'image' => $artist['images'][0]['url'] ?? null,
                'external_url' => $artist['external_urls']['spotify'] ?? null,
            ];
        });

        return response()->json($artists->values());
    }

    public function getTopGenres(Request $request)
    {
        $timeRange = $request->input('time_range', 'medium_term');
        $limit = $request->input('limit', 20);

        $data = $this->spotifyRequest('me/top/artists', [
            'limit' => $limit,
            'time_range' => $timeRange,
        ]);

        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']], $data['status']);
        }

        $genres = [];
        foreach ($data['items'] ?? [] as $artist) {
            foreach ($artist['genres'] ?? [] as $genre) {
                $genres[$genre] = ($genres[$genre] ?? 0) + 1;
            }
        }

        arsort($genres);
        $topGenres = array_slice($genres, 0, 5, true);

        $result = [];
        foreach ($topGenres as $genre => $count) {
            $result[] = ['genre' => $genre, 'count' => $count];
        }

        return response()->json($result);
    }

    public function getAudioFeatures(Request $request)
    {
        $timeRange = $request->input('time_range', 'long_term');
        $limit = $request->input('limit', 50);

        $tracksData = $this->spotifyRequest('me/top/tracks', [
            'limit' => $limit,
            'time_range' => $timeRange,
        ]);

        if (isset($tracksData['error'])) {
            return response()->json(['error' => $tracksData['error']], $tracksData['status']);
        }

        $trackIds = collect($tracksData['items'] ?? [])->pluck('id')->toArray();

        if (empty($trackIds)) {
            return response()->json([]);
        }

        $featuresData = $this->spotifyRequest('audio-features', [
            'ids' => implode(',', $trackIds),
        ]);

        if (isset($featuresData['error'])) {
            return response()->json(['error' => $featuresData['error']], $featuresData['status']);
        }

        $audioFeatures = collect($featuresData['audio_features'] ?? [])->map(function ($feature) {
            if (!$feature)
                return null;
            return [
                'id' => $feature['id'],
                'danceability' => $feature['danceability'],
                'energy' => $feature['energy'],
                'valence' => $feature['valence'],
                'tempo' => $feature['tempo'],
                'acousticness' => $feature['acousticness'],
                'instrumentalness' => $feature['instrumentalness'],
                'liveness' => $feature['liveness'],
            ];
        })->filter();

        return response()->json($audioFeatures->values());
    }

    public function getAudioFeaturesAverage(Request $request)
    {
        $timeRange = $request->input('time_range', 'long_term');
        $limit = $request->input('limit', 20);

        $tracksData = $this->spotifyRequest('me/top/tracks', [
            'limit' => $limit,
            'time_range' => $timeRange,
        ]);

        if (isset($tracksData['error'])) {
            return response()->json(['error' => $tracksData['error']], $tracksData['status']);
        }

        $trackIds = collect($tracksData['items'] ?? [])->pluck('id')->toArray();

        if (empty($trackIds)) {
            return response()->json([]);
        }

        $featuresData = $this->spotifyRequest('audio-features', [
            'ids' => implode(',', $trackIds),
        ]);

        if (isset($featuresData['error'])) {
            return response()->json(['error' => $featuresData['error']], $featuresData['status']);
        }

        $audioFeatures = collect($featuresData['audio_features'] ?? [])->filter();

        $featuresMedia = [
            'danceability' => 0,
            'energy' => 0,
            'valence' => 0,
            'tempo' => 0,
            'acousticness' => 0,
            'instrumentalness' => 0,
            'liveness' => 0,
        ];

        $validCount = count($audioFeatures);

        if ($validCount > 0) {
            foreach ($audioFeatures as $feature) {
                foreach ($featuresMedia as $key => $_) {
                    $featuresMedia[$key] += $feature[$key] ?? 0;
                }
            }
            foreach ($featuresMedia as $key => $value) {
                $featuresMedia[$key] = $value / $validCount;
            }
        }

        return response()->json($featuresMedia);
    }

    public function getExtremeTracks(Request $request)
    {
        $timeRange = $request->input('time_range', 'long_term');
        $limit = $request->input('limit', 20);

        $tracksData = $this->spotifyRequest('me/top/tracks', [
            'limit' => $limit,
            'time_range' => $timeRange,
        ]);

        if (isset($tracksData['error'])) {
            return response()->json(['error' => $tracksData['error']], $tracksData['status']);
        }

        $tracks = $tracksData['items'] ?? [];
        $trackIds = collect($tracks)->pluck('id')->toArray();

        if (empty($trackIds)) {
            return response()->json([]);
        }

        $featuresData = $this->spotifyRequest('audio-features', [
            'ids' => implode(',', $trackIds),
        ]);

        if (isset($featuresData['error'])) {
            return response()->json(['error' => $featuresData['error']], $featuresData['status']);
        }

        $audioFeatures = $featuresData['audio_features'] ?? [];

        $branoEstremo = [
            'piu_energetico' => null,
            'piu_felice' => null,
            'piu_acustico' => null,
        ];

        foreach ($audioFeatures as $i => $feature) {
            if (!$feature || !isset($tracks[$i])) continue;

            $track = $tracks[$i];
            $formattedTrack = $this->formatTrack($track);

            if (
                !$branoEstremo['piu_energetico'] ||
                $feature['energy'] > ($branoEstremo['piu_energetico']['energy'] ?? -INF)
            ) {
                $branoEstremo['piu_energetico'] = [
                    'track' => $formattedTrack,
                    'energy' => $feature['energy'],
                ];
            }

            if (
                !$branoEstremo['piu_felice'] ||
                $feature['valence'] > ($branoEstremo['piu_felice']['valence'] ?? -INF)
            ) {
                $branoEstremo['piu_felice'] = [
                    'track' => $formattedTrack,
                    'valence' => $feature['valence'],
                ];
            }

            if (
                !$branoEstremo['piu_acustico'] ||
                $feature['acousticness'] > ($branoEstremo['piu_acustico']['acousticness'] ?? -INF)
            ) {
                $branoEstremo['piu_acustico'] = [
                    'track' => $formattedTrack,
                    'acousticness' => $feature['acousticness'],
                ];
            }
        }

        return response()->json($branoEstremo);
    }
}
