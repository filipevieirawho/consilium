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
        if (!Schema::hasColumn('diagnosticos', 'empresa_id')) {
            Schema::table('diagnosticos', function (Blueprint $table) {
                $table->unsignedBigInteger('empresa_id')->nullable();
                $table->unsignedBigInteger('questionario_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnosticos', function (Blueprint $table) {
            $table->dropColumn('empresa_id');
            $table->dropColumn('questionario_id');
        });
    }
};
