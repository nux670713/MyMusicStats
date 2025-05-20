<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Utenti;
use App\Models\Amicizie;
use Illuminate\Foundation\Auth\User;

class AmicizieController extends Controller
{
    public function sendRequest($spotify_id)
    {
        $mittente = Auth::user()->id_utente;
        $destinatario = $spotify_id;

        if (!$mittente) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        //cerco il destinatario
        $destinatario = Utenti::where('spotify_id','=', $destinatario)->first();
        if (!$destinatario){
            return response()->json(['message' => 'Utente non trovato.'], 404);
        } 

        if($destinatario->id_utente == $mittente){
            return response()->json(['message'=> 'Non puoi inviare una richiesta di amicizia a te stesso.'], 400);
        }

        // Verifica se la richiesta di amicizia esiste già
        $richiesta = Amicizie::where('id_mittente','=', $mittente)
            ->where('id_destinatario', $destinatario->id_utente)
            ->first();
        if ($richiesta){
            return response()->json(['message'=> 'La richiesta è già stata inviata.'],400);
        }
        // Verifica se l'utente è già amico
        $richiesta1 = Amicizie::where('id_destinatario', '=', $mittente)
            ->where('id_mittente', $destinatario->id_utente)
            ->first();
        $richiesta2 = Amicizie::where('id_mittente', '=', $mittente)
            ->where('id_destinatario', $destinatario->id_utente)
            ->first();    
        if ($richiesta1 || $richiesta2){
            return response()->json(['message'=> 'Siete già amici.'],400);
        }    

        // Crea una nuova richiesta di amicizia
        $amicizia = Amicizie::create([
            'id_mittente' => $mittente,
            'id_destinatario' => $destinatario->id_utente,
            'status' => 'pending', // Stato della richiesta: 'pending', 'accepted', 'rejected'
        ]);

        return response()->json(['message' => 'Richiesta di amicizia inviata.', 'data'=> $amicizia],200);
    }

    public function acceptRequest($spotify_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        $mittente = $user->id_utente;
        $destinatario = $spotify_id;

        // controllo se il destinatario esiste
        $id_destinatario = Utenti::where('spotify_id','=', $destinatario)->first();
        if (!$id_destinatario) {
            return response()->json(['message' => 'Utente non trovato.'], 404);
        }
        $id_destinatario= $id_destinatario->id_utente;
        $accetta = Amicizie::where('id_mittente', '=', $id_destinatario)
            ->where('id_destinatario', '=', $mittente)
            ->where('status', '=', 'pending')
            ->first();

        if (!$accetta) {
            return response()->json(['message' => 'Richiesta di amicizia non trovata o già gestita.'], 404);
        }

        $accetta->status = 'accepted';
        $accetta->save();

        return response()->json(['message' => 'Richiesta di amicizia accettata.']);
    }

    public function rejectRequest($spotify_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $mittente = $spotify_id;
        $destinatario = $user->id_utente;

        // Controllo se esiste una richiesta di amicizia pendente
        $richiesta = Amicizie::where('id_mittente', '=', $mittente)
            ->where('id_destinatario', '=', $destinatario)
            ->where('status', '=', 'pending')
            ->first();

        if (!$richiesta) {
            return response()->json(['message' => 'Richiesta di amicizia non trovata o già gestita.'], 404);
        }

        // Aggiorna lo stato della richiesta a 'rejected'
        $richiesta->status = 'rejected';
        $richiesta->save();

        return response()->json(['message' => 'Richiesta di amicizia rifiutata.']);
    }

    public function cancelRequest($spotify_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $mittente = $user->id_utente;
        $destinatario = $spotify_id;

        // Controllo se esiste una richiesta di amicizia pendente inviata dall'utente
        $richiesta = Amicizie::where('id_mittente', '=', $mittente)
            ->where('id_destinatario', '=', function ($query) use ($destinatario) {
                $query->select('id_utente')
                    ->from('utenti')
                    ->where('spotify_id', '=', $destinatario)
                    ->limit(1);
            })
            ->where('status', '=', 'pending')
            ->first();

        if (!$richiesta) {
            return response()->json(['message' => 'Richiesta di amicizia non trovata o già gestita.'], 404);
        }

        // Elimina la richiesta di amicizia
        $richiesta->delete();

        return response()->json(['message' => 'Richiesta di amicizia annullata.']);
    }


    public function listFriends()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        $friends = Amicizie::where(function ($query) use ($user) {
            $query->where('id_mittente', '=', $user->id_utente)
              ->orWhere('id_destinatario', '=', $user->id_utente);
        })
        ->where('status', 'accepted')
        ->get()
        ->unique(function ($item) {
            return [$item->id_mittente, $item->id_destinatario];
        });

        $friendsList = $friends->map(function ($friend) use ($user) {
            $friendId = $friend->id_mittente == $user->id_utente ? $friend->id_destinatario : $friend->id_mittente;
            $friendUser = Utenti::find($friendId);
            if ($friendUser) {
                return [
                    'spotify_id' => $friendUser->spotify_id,
                    'username' => $friendUser->username,
                    'profile_picture' => $friendUser->profile_picture,
                    'email' => $friendUser->email,
                ];
            }
            return null;
        })->filter();

        return response()->json(['friends' => $friendsList]);
    }

    public function pendingRequests()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        $pendingRequests = Amicizie::where([
            ['id_destinatario', '=', $user->id_utente],
            ['status', '=', 'pending']
        ])->get();

        $pendingRequestsList = $pendingRequests->map(function ($request) {
            $requestUser = Utenti::find($request->id_mittente);
            if ($requestUser) {
                return [
                    'spotify_id' => $requestUser->spotify_id,
                    'username' => $requestUser->username,
                    'email' => $requestUser->email,
                ];
            }
            return null;
        })->filter();

        return response()->json(['pending_requests' => $pendingRequestsList]);
    }
    
    public function removeFriend($spotify_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }
        $mittente = $user->id_utente;
        $destinatario = $spotify_id;

        // Controllo se esiste una richiesta di amicizia pendente
        $amicizia = Amicizie::where(function ($query) use ($mittente, $destinatario) {
            $query->where('id_mittente', '=', $mittente)
                ->where('id_destinatario', '=', function ($query) use ($destinatario) {
                    $query->select('id_utente')
                        ->from('utenti')
                        ->where('spotify_id', '=', $destinatario)
                        ->limit(1);
                });
        })
        ->orWhere(function ($query) use ($mittente, $destinatario) {
            $query->where('id_destinatario', '=', $mittente)
                ->where('id_mittente', '=', function ($query) use ($destinatario) {
                    $query->select('id_utente')
                        ->from('utenti')
                        ->where('spotify_id', '=', $destinatario)
                        ->limit(1);
                });
        })
        ->first();

        if (!$amicizia) {
            return response()->json(['message' => 'Amicizia non trovata.'], 404);
        }

        // Elimina l'amicizia
        $amicizia->delete();

        return response()->json(['message' => 'Amicizia rimossa.']);
    }

    public function listByName($username)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $utenti = Utenti::where('username', 'LIKE', '%' . $username . '%')
            ->where('id_utente', '<>', $user->id_utente)
            ->get();

        return response()->json(['utenti' => $utenti]);
    }
}
