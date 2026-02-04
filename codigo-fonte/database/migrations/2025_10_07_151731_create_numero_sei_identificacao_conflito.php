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
        Schema::create('numero_sei_identificacao_conflito', function (Blueprint $table) {
            $table->id('idNumeroSeiIdentificacaoConflito');
            $table->foreignId('idConflito')->constrained('conflito');
            $table->string('numeroSei', 20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('numero_sei_identificacao_conflito');
    }
};
