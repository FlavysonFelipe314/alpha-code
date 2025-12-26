<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarefa extends Model
{
    use HasFactory;

    protected $table = 'tarefas';

    protected $fillable = [
        'titulo',
        'descricao',
        'prioridade',
        'tarefa_coluna_id',
        'ordem',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coluna()
    {
        return $this->belongsTo(TarefaColuna::class, 'tarefa_coluna_id');
    }

    public function pomodoros()
    {
        return $this->hasMany(Pomodoro::class);
    }
}
