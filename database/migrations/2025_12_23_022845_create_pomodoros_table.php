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
        Schema::create('pomodoros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
            $table->date('data');
            $table->integer('duracao_minutos')->default(25);
            $table->enum('tipo', ['produtividade', 'estudos', 'descanso'])->default('produtividade');
            $table->boolean('concluido')->default(false);
            $table->foreignId('tarefa_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pomodoros');
    }
};
