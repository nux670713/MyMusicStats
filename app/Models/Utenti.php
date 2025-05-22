<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * Class Utenti
 *
 * @package App\Models
 * @property int $id_utente
 * @property string $username
 * @property string $email
 * @property string|null $profile_picture
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property string $token_expires_at
 * @property string $spotify_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\Amicizie> $amicizie
 * @property-read int|null $amicizie_count
 * @property-read Collection<int, \App\Models\BraniRecenti> $brani_recenti
 * @property-read int|null $brani_recenti_count
 * @property-read Collection<int, \App\Models\Generi> $generi
 * @property-read int|null $generi_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, \App\Models\PostReaction> $post_likes
 * @property-read int|null $post_likes_count
 * @property-read Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read Collection<int, \App\Models\TopArtist> $top_artists
 * @property-read int|null $top_artists_count
 * @property-read Collection<int, \App\Models\TopTrack> $top_tracks
 * @property-read int|null $top_tracks_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti whereIdUtente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti whereProfilePicture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti whereSpotifyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti whereTokenExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Utenti whereUsername($value)
 * @mixin \Eloquent
 */
class Utenti extends Authenticatable

{
	use HasApiTokens, HasFactory, Notifiable;
	
	protected $table = 'utenti';
	protected $primaryKey = 'id_utente';
	public $timestamps = true;

	protected $hidden = [
		'access_token',
		'refresh_token'
	];

	protected $fillable = [
		'username',
		'email',
		'profile_picture',
		'spotify_id',
		'access_token',
		'refresh_token',
		'token_expires_at',
	];

	protected $dates = [
		'token_expires_at',
	];


	public function getValidAccessToken()
	{
		if (Carbon::now()->lt($this->token_expires_at)) {
			return $this->access_token;
		}

		$response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
			'grant_type' => 'refresh_token',
			'refresh_token' => $this->refresh_token,
			'client_id' => config('services.spotify.client_id'),
			'client_secret' => config('services.spotify.client_secret'),
		]);

		if ($response->successful()) {
			$data = $response->json();

			$this->access_token = $data['access_token'];
			$this->token_expires_at = Carbon::now()->addSeconds($data['expires_in']);
			$this->save();

			\Log::info("Spotify access token refreshed successfully for user ID: {$this->id_utente}");

			return $data['access_token'];
		}

		throw new \Exception('Spotify token refresh failed');
	}


	public function amicizie()
	{
		return $this->hasMany(Amicizie::class, 'id_destinatario');
	}

	public function brani_recenti()
	{
		return $this->hasMany(BraniRecenti::class, 'id_utente');
	}

	public function generi()
	{
		return $this->hasMany(Generi::class, 'id_utente');
	}

	public function post_reactions()
	{
		return $this->hasMany(PostReaction::class, 'id_utente');
	}

	public function posts()
	{
		return $this->hasMany(Post::class, 'id_utente');
	}

	public function top_artists()
	{
		return $this->hasMany(TopArtist::class, 'id_utente');
	}

	public function top_tracks()
	{
		return $this->hasMany(TopTrack::class, 'id_utente');
	}
}
