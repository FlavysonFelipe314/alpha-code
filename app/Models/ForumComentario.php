<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumComentario extends Model
{
    use HasFactory;

    protected $table = 'forum_comentarios';

    protected $fillable = [
        'user_id',
        'post_id',
        'comentario_pai_id',
        'conteudo',
        'curtidas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }

    public function pai()
    {
        return $this->belongsTo(ForumComentario::class, 'comentario_pai_id');
    }

    public function respostas()
    {
        return $this->hasMany(ForumComentario::class, 'comentario_pai_id');
    }
}