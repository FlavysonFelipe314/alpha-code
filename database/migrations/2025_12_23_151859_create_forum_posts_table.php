<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('forum_categorias')->onDelete('cascade');
            $table->string('titulo');
            $table->text('conteudo');
            $table->boolean('fixado')->default(false);
            $table->boolean('fechado')->default(false);
            $table->integer('visualizacoes')->default(0);
            $table->integer('curtidas')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};