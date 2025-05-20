<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id_brano
 * @property int $id_utente
 * @property int $rank
 * @property string $id_spotify
 * @property string $track_name
 * @property string $artist
 * @property string|null $time_range
 * @property string|null $img_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Utenti $utente
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack whereArtist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack whereIdBrano($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack whereIdSpotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack whereIdUtente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack whereTimeRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack whereTrackName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TopTrack whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TopTrack extends Model
{
	protected $table = 'top_tracks';
	protected $primaryKey = 'id_brano';
	public $timestamps = true;

	protected $fillable = [
		'id_utente',
		'id_spotify', 
		'track_name',
		'artist', 
		'time_range',
		'img_url',
		'rank',
	];

	public function utente()
	{
		return $this->belongsTo(Utenti::class, 'id_utente', 'id_utente');
	}
}