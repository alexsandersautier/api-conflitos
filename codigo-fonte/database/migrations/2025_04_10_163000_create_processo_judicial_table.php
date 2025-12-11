<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('processo_judicial', function (Blueprint $table) {
            $table->id('idProcessoJudicial')->primary();
            $table->foreignId('idConflito')->constrained('conflito');
            $table->date('data')->nullable();
            $table->string('numero', 50)->nullable();
            $table->string('tipoPoder')->nullable();
            $table->string('orgaoApoio')->nullable();
            $table->integer('numeroSei')->nullable();
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
