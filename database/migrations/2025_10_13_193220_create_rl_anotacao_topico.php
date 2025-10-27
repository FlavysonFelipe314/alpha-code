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
        Schema::create('rl_anotacao_topico', function (Blueprint $table) {
            $table->engine = 'InnoDB'; 
            $table->id();
            $table->foreignId('anotacao_id')
            ->constrained('anotacoes')
            ->onDelete('CASCADE')
            ->onUpdate('CASCADE');
            $table->foreignId('topico_anotacao_id')
            ->constrained('topicos_anotacoes')->onDelete('cascade')
            ->onDelete('CASCADE')
            ->onUpdate('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rl_anotacao_topico');
    }
};
