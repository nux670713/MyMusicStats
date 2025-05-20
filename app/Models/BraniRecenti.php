<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id_brano
 * @property int $id_utente
 * @property string $id_spotify
 * @property string $track_name
 * @property string $artist
 * @property string $album
 * @property string|null $img_url
 * @property string|null $played_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Utenti $utente
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti whereAlbum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti whereArtist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti whereIdBrano($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti whereIdSpotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti whereIdUtente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti wherePlayedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti whereTrackName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BraniRecenti whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BraniRecenti extends Model
{
	protected $table = 'brani_recenti';
	protected $primaryKey = 'id_brano';
	public $timestamps = true;

	protected $fillable = [
		'id_utente',
		'id_spotify',
		'track_name',
		'artist',
		'album',
		'img_url',
		'played_at',
	];



	public function utente()
	{
		return $this->belongsTo(Utenti::class, 'id_utente', 'id_utente');
	}
}