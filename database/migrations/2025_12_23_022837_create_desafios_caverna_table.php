<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('desafios_caverna', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->enum('status', ['pendente', 'em_andamento', 'concluido', 'cancelado'])->default('pendente');
            $table->json('metas')->nullable(); // Array de metas do desafio
            $table->json('progresso')->nullable(); // Progresso do desafio
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desafios_caverna');
    }
};
