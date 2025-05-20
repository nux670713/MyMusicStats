<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReaction extends Model
{
	protected $table = 'post_reactions';
	protected $primaryKey = null;
	public $incrementing = false;
	protected $keyType = 'string';
	public $timestamps = true;

	protected $fillable = [
		'id_post',
		'id_utente',
		'reaction_type',
	];

	public function post()
	{
		return $this->belongsTo(Post::class, 'id_post');
	}

	public function utente()
	{
		return $this->belongsTo(Utenti::class, 'id_utente');
	}
}
