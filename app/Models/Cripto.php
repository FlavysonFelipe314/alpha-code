<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cripto extends Model
{
    /** @use HasFactory<\Database\Factories\CriptoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'moeda',
        'saldo',
        'observacao',
        'user_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
