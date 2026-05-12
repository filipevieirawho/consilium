<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diagnosticos', function (Blueprint $table) {
            $table->foreignId('sessao_id')
                  ->nullable()
                  ->after('questionario_id')
                  ->constrained('diagnostico_sessoes')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('diagnosticos', function (Blueprint $table) {
            $table->dropForeign(['sessao_id']);
            $table->dropColumn('sessao_id');
        });
    }
};
