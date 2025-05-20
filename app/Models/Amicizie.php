<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Amicizie
 *
 * @package App\Models
 * @property int $id_amicizia
 * @property int $id_mittente
 * @property int $id_destinatario
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Utenti $utenti
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amicizie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amicizie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amicizie query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amicizie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amicizie whereIdAmicizia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amicizie whereIdDestinatario($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amicizie whereIdMittente($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amicizie whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Amicizie whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Amicizie extends Model
{
	protected $table = 'amicizie';
	public $incrementing = true;
	protected $primaryKey = 'id_amicizia';
	
	public $timestamps = true;

	protected $casts = [
		'id_mittente' => 'int',
		'id_destinatario' => 'int'
	];

	protected $fillable = [
		'id_mittente',
		'id_destinatario',
		'status'
	];

	public function utenti()
	{
		return $this->belongsTo(Utenti::class, 'id_destinatario');
	}
}
