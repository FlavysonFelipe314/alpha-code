<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anotacao extends Model
{
    /** @use HasFactory<\Database\Factories\AnotacaoFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'anotacoes';
    
    protected $with = ['user', 'topico'];

    protected $fillable = [
        'name',
        'content',
        'user_id',
        'topico_anotacao_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function topico()
    {
        return $this->belongsTo(TopicoAnotacao::class, 'topico_anotacao_id');
    }

}
