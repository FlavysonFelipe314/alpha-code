<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ritual extends Model
{
    use HasFactory;

    protected $table = 'rituais';

    protected $fillable = [
        'nome',
        'tipo',
        'horario_inicio',
        'horario_fim',
        'ordem',
        'descricao',
        'ativo',
        'user_id',
    ];

    protected $casts = [
        'horario_inicio' => 'datetime',
        'horario_fim' => 'datetime',
        'ativo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
