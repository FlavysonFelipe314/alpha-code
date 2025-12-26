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
        Schema::create('rituais', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('tipo', ['matinal', 'noturno']);
            $table->time('horario_inicio');
            $table->time('horario_fim')->nullable();
            $table->integer('ordem')->default(0);
            $table->text('descricao')->nullable();
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
        Schema::dropIfExists('rituais');
    }
};
