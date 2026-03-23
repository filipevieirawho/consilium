<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();

            // Dados do respondente
            $table->string('nome')->nullable();
            $table->string('cargo')->nullable();
            $table->string('empresa')->nullable();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();

            // Dados do empreendimento
            $table->string('nome_empreendimento')->nullable();
            $table->string('cidade')->nullable();
            $table->string('tipologia')->nullable();
            $table->integer('num_torres')->nullable();
            $table->integer('estagio_obra')->nullable(); // % aprox
            $table->integer('prazo_inicial')->nullable(); // meses
            $table->integer('prazo_atual')->nullable();   // meses
            $table->boolean('aceite')->default(false);

            // Resultado
            $table->float('ipm')->nullable();
            $table->string('status')->default('em_andamento'); // em_andamento | concluido

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};
