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
        Schema::create('processo_sei', function (Blueprint $table) {
            $table->id('idProcessoSei')->primary();
            $table->foreignId('idTipoProcessoSei')->constrained('tipo_processo_sei');
            $table->foreignId('idConflito')->constrained('conflito');
            $table->string('numero', 50);
            $table->string('assunto', 50);
            $table->string('especificacao', 50);
            $table->string('interessado', 50);
            $table->timestamps();
        });

        Schema::create('tipo_processo_sei', function (Blueprint $table) {
            $table->id('idTipoProcessoSei')->primary();
            $table->string('nome', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processo_sei');
        Schema::dropIfExists('tipo_processo_sei');
    }
};
