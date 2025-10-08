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
        Schema::create('programa_protecao', function (Blueprint $table) {
            $table->id('idProgramaProtecao')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->string('tipoPrograma');
            $table->string('uf',2);
            $table->string('numeroSei', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programa_protecao');
    }
};
