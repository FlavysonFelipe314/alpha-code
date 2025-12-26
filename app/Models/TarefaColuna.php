<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarefaColuna extends Model
{
    use HasFactory;

    protected $table = 'tarefa_colunas';

    protected $fillable = [
        'nome',
        'ordem',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tarefas()
    {
        return $this->hasMany(Tarefa::class)->orderBy('ordem');
    }
}
