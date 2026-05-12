<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questionarios', function (Blueprint $table) {
            $table->text('texto_resultado_red')->nullable()->after('descricao');
            $table->text('texto_resultado_yellow')->nullable()->after('texto_resultado_red');
            $table->text('texto_resultado_green')->nullable()->after('texto_resultado_yellow');
        });
    }

    public function down(): void
    {
        Schema::table('questionarios', function (Blueprint $table) {
            $table->dropColumn(['texto_resultado_red', 'texto_resultado_yellow', 'texto_resultado_green']);
        });
    }
};
