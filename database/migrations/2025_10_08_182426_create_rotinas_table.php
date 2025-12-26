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
        Schema::create('rotinas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['diaria', 'semanal', 'mensal'])->default('diaria');
            $table->json('dias_semana')->nullable(); // Para rotinas semanais: ['segunda', 'quarta', 'sexta']
            $table->time('horario')->nullable();
            $table->boolean('ativo')->default(true);
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rotinas');
    }
};
