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
        Schema::create('receitas', function (Blueprint $table) {
            $table->engine = 'InnoDB'; 
            $table->id();
            $table->string('titulo', 255);
            $table->string('categoria', 255);
            $table->decimal('valor', 10,2);
            $table->date('recebimento');
            $table->text('observacao')->nullable();
            $table->integer('efetivado')->default(0);
            $table->foreignId('user_id')
            ->constrained('users')
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receitas');
    }
};
