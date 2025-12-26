<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $table = 'forum_posts';

    protected $fillable = [
        'user_id',
        'categoria_id',
        'titulo',
        'conteudo',
        'fixado',
        'fechado',
        'visualizacoes',
        'curtidas',
    ];

    protected $casts = [
        'fixado' => 'boolean',
        'fechado' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria()
    {
        return $this->belongsTo(ForumCategoria::class, 'categoria_id');
    }

    public function comentarios()
    {
        return $this->hasMany(ForumComentario::class, 'post_id')->whereNull('comentario_pai_id');
    }

    public function todosComentarios()
    {
        return $this->hasMany(ForumComentario::class, 'post_id');
    }

    public function incrementarVisualizacao()
    {
        $this->increment('visualizacoes');
    }
}