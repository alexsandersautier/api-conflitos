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
        Schema::create('povo', function (Blueprint $table) {
            $table->id('idPovo')->primary();
            $table->string('nome', 200);
            $table->string('codEtnia', 50);
            $table->string('lingua', 200)->nullable();
            $table->string('familia_linguistica', 200)->nullable();
            $table->string('ufs_povo', 200)->nullable();
            $table->integer('qtd_ti_povo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('povo');
    }
};
