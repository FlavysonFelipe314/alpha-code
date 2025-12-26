<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plano_id',
        'asaas_subscription_id',
        'asaas_customer_id',
        'status',
        'inicio',
        'fim',
        'proximo_pagamento',
        'valor',
        'dados_pagamento',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim' => 'datetime',
        'proximo_pagamento' => 'datetime',
        'valor' => 'decimal:2',
        'dados_pagamento' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }
}