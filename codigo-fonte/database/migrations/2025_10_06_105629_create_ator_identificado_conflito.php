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
        Schema::create('ator_identificado_conflito', function (Blueprint $table) {
            $table->id('idAtorIdentificadoConflito');
            $table->foreignId('idConflito')->constrained('conflito');
            $table->string('nome', 200);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ator_identificado_conflito');
    }
};
