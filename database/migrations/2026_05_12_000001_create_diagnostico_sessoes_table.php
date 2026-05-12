<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostico_sessoes', function (Blueprint $table) {
            $table->id();
            $table->string('token', 60)->unique();
            $table->foreignId('questionario_id')->nullable()->constrained('questionarios')->nullOnDelete();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostico_sessoes');
    }
};
