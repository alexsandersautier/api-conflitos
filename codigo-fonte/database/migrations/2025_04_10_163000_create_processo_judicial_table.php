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
        Schema::create('processo_judicial', function (Blueprint $table) {
            $table->id('idProcessoJudicial')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->date('data');
            $table->string('numero', 50);
            $table->string('tipoPoder');
            $table->string('orgaoApoio');
            $table->integer('numeroSei');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processo_judicial');
    }
};
