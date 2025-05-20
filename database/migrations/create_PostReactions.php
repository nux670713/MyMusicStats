<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
Schema::create('post_reactions', function (Blueprint $table) {
$table->id('id_reaction');
$table->unsignedBigInteger('id_post');
$table->unsignedBigInteger('id_utente');
$table->string('reaction_type'); // tipo "like", "love", "laugh", ecc.
$table->timestamps();

$table->foreign('id_post')->references('post_id')->on('posts')->onDelete('cascade');
$table->foreign('id_utente')->references('id_utente')->on('utenti')->onDelete('cascade');
});