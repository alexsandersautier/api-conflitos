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
        Schema::create('ator', function (Blueprint $table) {
            $table->id('idAtor')->primary();
            $table->string('nome', 200);
        });

        Schema::create('categoria_ator', function (Blueprint $table) {
            $table->id('idCategoriaAtor')->primary();
            $table->string('nome', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ator');
        Schema::dropIfExists('categoria_ator');
    }
};
