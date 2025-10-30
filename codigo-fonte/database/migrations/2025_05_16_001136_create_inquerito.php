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
        Schema::create('inquerito', function (Blueprint $table) {
            $table->id('idInqueritoPolicial')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->date('data')->nullable();
            $table->string('numero', 50)->nullable();
            $table->string('orgao', 200)->nullable();
            $table->string('tipoOrgao', 100)->nullable();
            $table->string('numeroSei', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquerito');
    }
};
