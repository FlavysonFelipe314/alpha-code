<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assinaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plano_id')->constrained()->onDelete('cascade');
            $table->string('asaas_subscription_id')->nullable()->unique(); // ID da assinatura no Asaas
            $table->string('asaas_customer_id')->nullable(); // ID do cliente no Asaas
            $table->string('status')->default('pending'); // pending, active, canceled, expired
            $table->dateTime('inicio')->nullable();
            $table->dateTime('fim')->nullable();
            $table->dateTime('proximo_pagamento')->nullable();
            $table->decimal('valor', 10, 2);
            $table->json('dados_pagamento')->nullable(); // Dados adicionais do pagamento
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assinaturas');
    }
};