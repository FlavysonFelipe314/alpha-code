<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Custo extends Model
{
    /** @use HasFactory<\Database\Factories\CustoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titulo',
        'tipo',
        'forma_pagamento',
        'categoria',
        'custo',
        'pagamento',
        'observacao',
        'efetivado',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
