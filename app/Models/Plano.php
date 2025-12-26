<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'moeda',
        'periodicidade',
        'dias_trial',
        'asaas_product_id',
        'ativo',
        'ordem',
        'features',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'ativo' => 'boolean',
        'features' => 'array',
    ];

    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class);
    }
}