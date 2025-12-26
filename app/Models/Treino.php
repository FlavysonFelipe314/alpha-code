<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treino extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'data',
        'day',
        'horario',
        'observacoes',
        'peso_atual',
        'objetivo',
        'shape',
        'realizado',
        'user_id',
    ];

    protected $casts = [
        'data' => 'date',
        'horario' => 'datetime',
        'realizado' => 'boolean',
        'peso_atual' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
