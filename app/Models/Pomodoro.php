<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pomodoro extends Model
{
    use HasFactory;

    protected $table = 'pomodoros';

    protected $fillable = [
        'user_id',
        'data',
        'duracao_minutos',
        'tipo',
        'concluido',
        'tarefa_id',
    ];

    protected $casts = [
        'data' => 'date',
        'concluido' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tarefa()
    {
        return $this->belongsTo(Tarefa::class);
    }
}
