<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id_genere
 * @property int $id_utente
 * @property string $genere
 * @property int|null $count
 * @property string|null $time_range
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Utenti $utente
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Generi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Generi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Generi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Generi whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Generi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Generi whereGenere($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Generi whereIdGenere($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Generi whereIdUtente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Generi whereTimeRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Generi whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Generi extends Model
{
	protected $table = 'generi';
	protected $primaryKey = 'id_genere'; // Corretto il nome della chiave primaria
	public $timestamps = true;

	protected $fillable = [
		'id_utente',
		'genere',
		'count',
		'time_range',
		'created_at',
		'updated_at',
	];

	public function utente()
	{
		return $this->belongsTo(Utenti::class, 'id_utente', 'id_utente');
	}
}