<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModoCaverna extends Model
{
    use HasFactory;

    protected $table = 'modo_caverna';

    protected $fillable = [
        'user_id',
        'dias_consecutivos',
        'ultimo_acesso',
        'ativo',
    ];

    protected $casts = [
        'ultimo_acesso' => 'date',
        'ativo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
