<?php
/**
 * Class Post
 *
 * @property int $id_post
 * @property int $id_utente
 * @property string $type
 * @property array $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, \App\Models\PostReaction> $post_reactions
 * @property-read int|null $post_reactions_count
 * @property-read \App\Models\Utenti $utenti
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class Post extends Model
{
protected $table = 'posts';
protected $primaryKey = 'id_post';
public $incrementing = true;
protected $keyType = 'int';
public $timestamps = true;

protected $casts = [
'id_utente' => 'int',
'content' => 'array',
];

protected $fillable = [
'type',
'id_utente',
'content',
];

public function utenti()
{
return $this->belongsTo(Utenti::class, 'id_utente');
}

public function post_reactions()
{
return $this->hasMany(PostReaction::class, 'id_post', 'id_post');
}
}