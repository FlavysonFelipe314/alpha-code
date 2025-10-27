<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicoAnotacao extends Model
{
    protected $table = 'topicos_anotacoes';
    
    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function anotacoes()
    {
        return $this->hasMany(Anotacao::class, 'topico_anotacao_id');
    }

}
