<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostico_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostico_id')->constrained('diagnosticos')->cascadeOnDelete();
            $table->unsignedTinyInteger('dimensao'); // 1–6
            $table->unsignedTinyInteger('pergunta'); // 1–18
            $table->unsignedTinyInteger('resposta'); // 0–3
            $table->timestamps();

            $table->unique(['diagnostico_id', 'pergunta']); // one answer per question
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostico_respostas');
    }
};
