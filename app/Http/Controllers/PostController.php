<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Amicizie;
use App\Models\Utenti;

class PostController extends Controller
{
public function index(){
    return view("posts.create");
}



public function createPost(Request $request)
{
    if ($request->isMethod('post')) {
        $request->validate([
            'type' => 'required|string', // Tipi di post validi
            'content' => 'required|array', // Contenuto in formato JSON
        ]);

        $user = Auth::user();

        $post = new Post();
        $post->id_utente = $user->id_utente;
        $post->type = $request->input('type');
        $post->content = $request->input('content'); // Assegna direttamente l'array/oggetto JSON
        $post->save();

        return response()->json(['message' => 'Post creato con successo.', 'post' => $post], 201);
    }

    return response()->json(['message' => 'Metodo non consentito.'], 405);
}

    /**
     * Aggiungi o aggiorna una reazione a un post.
     */
/*
 * Aggiungi o aggiorna una reazione a un post.
 */
public function reactToPost(Request $request, $id, $reaction_type)
{
    if (!in_array($reaction_type, ['like', 'love', 'laugh', 'angry'])) {
        return response()->json(['message' => 'Tipo di reazione non valido.'], 400);
    }

    $user = Auth::user();

    // Cerca se l'utente ha già reagito al post
    $existingReaction = PostReaction::where('id_post', $id)
        ->where('id_utente', $user->id_utente)
        ->first();

    if ($existingReaction) {
        // Se stessa reaction, rimuovila (toggle off)
        if ($existingReaction->reaction_type === $reaction_type) {
            PostReaction::where('id_post', $id)
                ->where('id_utente', $user->id_utente)
                ->delete();
            return response()->json(['message' => 'Reazione rimossa.']);
        }

        // Se diversa, aggiorna la reazione
        $existingReaction->reaction_type = $reaction_type;
        $existingReaction->save();
        return response()->json(['message' => 'Reazione aggiornata.']);
    }

    // Se non esiste nessuna reazione, creane una nuova
    $reaction = new PostReaction();
    $reaction->id_post = $id;
    $reaction->id_utente = $user->id_utente;
    $reaction->reaction_type = $reaction_type;
    $reaction->save();

    return response()->json(['message' => 'Reazione aggiunta.']);
}

public function getMyReaction($id){
    $user = Auth::user();

    // Cerca la reazione dell'utente al post
    $reaction = PostReaction::where('id_post', $id)
        ->where('id_utente', $user->id_utente)
        ->first();

    if ($reaction) {
        return response()->json(['reaction' => $reaction->reaction_type]);
    }
}

    /**
     * Recupera il feed dei post.
     */
  
/**
 * Recupera il feed dei post (propri e degli amici).
 */
public function feed()
{
    $user = Auth::user();

    // Recupera gli ID degli amici
    $friendIds = Amicizie::where(function ($query) use ($user) {
        $query->where('id_mittente', $user->id_utente)
              ->orWhere('id_destinatario', $user->id_utente);
    })
    ->where('status', 'accepted')
    ->get()
    ->map(function ($friendship) use ($user) {
        return $friendship->id_mittente === $user->id_utente
            ? $friendship->id_destinatario
            : $friendship->id_mittente;
    });

    // Aggiungi l'ID dell'utente autenticato per includere i propri post
    $userAndFriendIds = $friendIds->push($user->id_utente);

    // Recupera i post dell'utente e degli amici
    $posts = Post::whereIn('id_utente', $userAndFriendIds)
        ->with(['utenti:id_utente,username,profile_picture']) // Include i dettagli dell'utente
        ->withCount('post_reactions') // Conta le reazioni
        ->orderBy('created_at', 'desc')
        ->get();

    // Decodifica il contenuto JSON per ogni post
    $posts->transform(function ($post) {
        if (is_string($post->content)) {
            $post->content = json_decode($post->content, true);
        }
        return $post;
    });

    return response()->json(['posts' => $posts]);
}

    /**
     * Elimina un post.
     */
    public function deletePost($id)
    {
        $user = Auth::user();

        $post = Post::where('id_post', $id)
            ->where('id_utente', $user->id_utente)
            ->first();

        if (!$post) {
            return response()->json(['message' => 'Post non trovato o non autorizzato.'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Post eliminato con successo.']);
    }

    /**
     * Recupera un singolo post.
     */
    public function getPost($id)
    {
        $post = Post::withCount('post_reactions')->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post non trovato.'], 404);
        }

        $post->content = json_decode($post->content, true);

        return response()->json(['post' => $post]);
    }
}