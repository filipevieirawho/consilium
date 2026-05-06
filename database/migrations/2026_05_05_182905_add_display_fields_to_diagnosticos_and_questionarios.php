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
        Schema::table('diagnosticos', function (Blueprint $table) {
            $table->string('titulo')->nullable()->after('token');
            $table->text('subtitulo')->nullable()->after('titulo');
            $table->text('descricao')->nullable()->after('subtitulo');
        });

        Schema::table('questionarios', function (Blueprint $table) {
            $table->text('descricao')->nullable()->after('subtitulo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diagnosticos', function (Blueprint $table) {
            $table->dropColumn(['titulo', 'subtitulo', 'descricao']);
        });

        Schema::table('questionarios', function (Blueprint $table) {
            $table->dropColumn('descricao');
        });
    }
};
