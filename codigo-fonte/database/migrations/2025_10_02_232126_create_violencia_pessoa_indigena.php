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
        Schema::create('violencia_pessoa_indigena', function (Blueprint $table) {
            $table->id('idViolenciaPessoaIndigena')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->string('tipoViolencia', 100);
            $table->date('data');
            $table->string('nome', 200);
            $table->integer('idade');
            $table->string('faixaEtaria', 50);
            $table->string('genero', 50);
            $table->string('instrumentoViolencia', 200);
            $table->string('numeroSei', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violencia_pessoa_indigena');
    }
};
