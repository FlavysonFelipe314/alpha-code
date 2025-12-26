<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->decimal('preco', 10, 2);
            $table->string('moeda', 3)->default('BRL');
            $table->string('periodicidade')->default('monthly'); // monthly, yearly
            $table->integer('dias_trial')->default(0);
            $table->string('asaas_product_id')->nullable(); // ID do produto no Asaas
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0);
            $table->json('features')->nullable(); // Lista de features do plano
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};