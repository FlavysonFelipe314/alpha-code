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
        Schema::table('bibliotecas', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('notes');
            $table->string('file_type')->nullable()->after('file_path'); // 'pdf', 'video', 'audio'
            $table->foreignId('user_id')->after('id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bibliotecas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['file_path', 'file_type', 'user_id']);
        });
    }
};
