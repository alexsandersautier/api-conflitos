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
        Schema::create('registro_bo_nf', function (Blueprint $table) {
            $table->id('idRegistroBoNf')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->date('data');
            $table->integer('numero');
            $table->string('orgao', 100);
            $table->string('tipoOrgao', 100);
            $table->integer('numeroSei');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_bo_nf');
    }
};
