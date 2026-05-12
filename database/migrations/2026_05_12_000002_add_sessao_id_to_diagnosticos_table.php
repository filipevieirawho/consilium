<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Turso/SQLite não suporta FK constraints em ALTER TABLE.
        // Adicionamos sessao_id como integer nullable simples; a relação
        // é garantida em nível de aplicação pelo Eloquent.
        Schema::table('diagnosticos', function (Blueprint $table) {
            $table->unsignedBigInteger('sessao_id')->nullable()->after('questionario_id');
        });
    }

    public function down(): void
    {
        Schema::table('diagnosticos', function (Blueprint $table) {
            $table->dropColumn('sessao_id');
        });
    }
};
