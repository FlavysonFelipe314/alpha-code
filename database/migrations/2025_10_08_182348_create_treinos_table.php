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
        Schema::create('treinos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->date('data');
            $table->time('horario')->nullable();
            $table->text('observacoes')->nullable();
            $table->decimal('peso_atual', 5, 2)->nullable();
            $table->string('objetivo')->nullable(); // Perder peso, ganhar massa, etc
            $table->string('shape')->nullable(); // Definido, Indefinido, etc
            $table->boolean('realizado')->default(false);
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
        Schema::dropIfExists('treinos');
    }
};
