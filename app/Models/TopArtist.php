<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id_artista
 * @property int $id_utente
 * @property int $rank
 * @property string $id_spotify
 * @property string $artist
 * @property string|null $time_range
 * @property string|null $img_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Utenti $utente
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist whereArtist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist whereIdArtista($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist whereIdSpotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist whereIdUtente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist whereTimeRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopArtist whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TopArtist extends Model
{
	protected $table = 'top_artists';
	protected $primaryKey = 'id_artista';
	public $timestamps = true;

	protected $fillable = [
		'id_utente',
		'id_spotify', 
		'artist', 
		'time_range',
		'img_url',
		'rank',
		'created_at',
	];

	public function utente()
	{
		return $this->belongsTo(Utenti::class, 'id_utente', 'id_utente');
	}
}