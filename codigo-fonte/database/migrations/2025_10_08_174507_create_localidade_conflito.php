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
        Schema::create('localidade_conflito', function (Blueprint $table) {
            $table->id('idLocalidadeConflito');
            $table->foreignId('idConflito')->constrained('conflito');
            $table->string('regiao');
            $table->string('uf', 2);
            $table->string('municipio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localidade_conflito');
    }
};
