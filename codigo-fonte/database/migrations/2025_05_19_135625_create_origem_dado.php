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
        Schema::create('origem_dado', function (Blueprint $table) {
            $table->id('idOrigemDado')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->foreignId('idTipoResponsavel')->constrained('tipo_responsavel');
            $table->string('setor_cadastrante', 200);
            $table->longText('observacao')->nullable();
            $table->timestamps();
        });
        
        Schema::create('tipo_responsavel', function (Blueprint $table) {
            $table->id('idTipoResponsavel')->primary();
            $table->string('nome', 200);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('origem_dado');
        Schema::dropIfExists('tipo_responsavel');
    }
};
