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
        if (!Schema::hasColumn('diagnostico_respostas', 'questao_id')) {
            Schema::table('diagnostico_respostas', function (Blueprint $table) {
                $table->unsignedBigInteger('questao_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnostico_respostas', function (Blueprint $table) {
            $table->dropColumn('questao_id');
        });
    }
};
