<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesafioCaverna extends Model
{
    use HasFactory;

    protected $table = 'desafios_caverna';

    protected $fillable = [
        'user_id',
        'titulo',
        'descricao',
        'data_inicio',
        'data_fim',
        'status',
        'metas',
        'progresso',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'metas' => 'array',
        'progresso' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
