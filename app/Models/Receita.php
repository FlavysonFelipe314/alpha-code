<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receita extends Model
{
    /** @use HasFactory<\Database\Factories\ReceitaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titulo',
        'categoria',
        'valor',
        'recebimento',
        'observacao',
        'efetivado',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
