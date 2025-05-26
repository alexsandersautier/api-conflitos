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
        Schema::create('lideranca_ameacada', function (Blueprint $table) {
            $table->id('idLiderancaAmeacada')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->string('nome', 100);
            $table->decimal('distancia_conflito', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lideranca_ameacada');
    }
};
