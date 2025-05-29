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
        Schema::create('ator', function (Blueprint $table) {
            $table->id('idAtor')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->foreignId('idTipoAtor')->constrained('tipo_ator');
            $table->string('nome', 100);
            $table->timestamps();
        });
        
        Schema::create('tipo_ator', function (Blueprint $table) {
            $table->id('idTipoAtor')->primary();
            $table->string('nome', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ator');
        Schema::dropIfExists('tipo_ator');
    }
};
