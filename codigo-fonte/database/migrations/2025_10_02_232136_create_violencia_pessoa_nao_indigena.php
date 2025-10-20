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
        Schema::create('violencia_pessoa_nao_indigena', function (Blueprint $table) {
            $table->id('idViolenciaPessoaNaoIndigena')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->string('tipoViolencia', 100)->nullable();
            $table->string('tipoPessoa', 100)->nullable();
            $table->date('data')->nullable();
            $table->string('nome', 200)->nullable();
            $table->string('numeroSei', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violencia_pessoa_nao_indigena');
    }
};
