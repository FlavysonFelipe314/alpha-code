<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCategoria extends Model
{
    use HasFactory;

    protected $table = 'forum_categorias';

    protected $fillable = [
        'nome',
        'descricao',
        'cor',
        'icone',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(ForumPost::class, 'categoria_id');
    }
}