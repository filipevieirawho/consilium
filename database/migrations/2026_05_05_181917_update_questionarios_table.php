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
        Schema::table('questionarios', function (Blueprint $table) {
            $table->renameColumn('titulo', 'nome');
            $table->string('titulo')->nullable()->after('nome');
            $table->text('subtitulo')->nullable()->after('titulo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questionarios', function (Blueprint $table) {
            $table->renameColumn('nome', 'titulo');
            $table->dropColumn(['titulo', 'subtitulo']);
        });
    }
};
