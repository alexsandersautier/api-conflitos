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
        Schema::create('inquerito_policial', function (Blueprint $table) {
            $table->id('idInqueritoPolicial')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->foreignId('idTipoInqueritoPolicial')->constrained('tipo_inquerito_policial');
            $table->string('numero_bo', 50);
            $table->date('data');
            $table->string('assistencia_juridica', 50);
            $table->timestamps();
        });
        
        Schema::create('tipo_inquerito_policial', function (Blueprint $table) {
            $table->id('idTipoInqueritoPolicial')->primary();
            $table->string('nome', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquerito_policial');
        Schema::dropIfExists('tipo_inquerito_policial');
    }
};
