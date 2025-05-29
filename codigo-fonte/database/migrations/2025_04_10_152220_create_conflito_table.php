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
        Schema::create('conflito', function (Blueprint $table) {
            $table->id('idConflito')->primary();
            $table->foreignId('idTerraIndigena')->constrained('terra_indigena');
            $table->foreignId('idPovo')->constrained('povo');
            $table->string('nome', 200);
            $table->longText('descricao')->nullable();
            $table->string('regiao', 200)->nullable();
            $table->date('dataInicioConflito');
            $table->date('dataFimConflito')->nullable();
            $table->string('latitude', 200)->nullable();
            $table->string('longitude', 200)->nullable();
            $table->string('municipio', 200)->nullable();
            $table->string('uf', 2)->nullable();
            $table->boolean('flagOcorrenciaAmeaca')->default(0);
            $table->boolean('flagOcorrenciaViolencia')->default(0);
            $table->boolean('flagOcorrenciaAssassinato')->default(0);
            $table->boolean('flagOcorrenciaFeridos')->default(0);
            $table->boolean('flagMembroProgramaProtecao')->default(0);
            $table->timestamps();
        });

        Schema::create('assunto_conflito', function (Blueprint $table) {
            // Chaves estrangeiras
            $table->unsignedBigInteger('idConflito');
            $table->unsignedBigInteger('idAssunto');
            
            // Chave primária composta
            $table->primary(['idConflito', 'idAssunto']);

            // Constraints de chave estrangeira
            $table->foreign('idConflito')
                  ->references('idConflito')
                  ->on('conflito')
                  ->onDelete('cascade');

            $table->foreign('idAssunto')
                  ->references('idAssunto')
                  ->on('assunto')
                  ->onDelete('cascade');
        });

        Schema::create('conflito_tipo_conflito', function (Blueprint $table) {
            // Chaves estrangeiras
            $table->unsignedBigInteger('idConflito');
            $table->unsignedBigInteger('idTipoConflito');

            // Chave primária composta
            $table->primary(['idConflito', 'idTipoConflito']);

            // Constraints de chave estrangeira
            $table->foreign('idConflito')
                  ->references('idConflito')
                  ->on('conflito')
                  ->onDelete('cascade');

            $table->foreign('idTipoConflito')
                  ->references('idTipoConflito')
                  ->on('tipo_conflito')
                  ->onDelete('cascade');
        });

        Schema::create('impacto_ambiental_conflito', function (Blueprint $table) {
            // Chaves estrangeiras
            $table->unsignedBigInteger('idConflito');
            $table->unsignedBigInteger('idImpactoAmbiental');

            // Chave primária composta
            $table->primary(['idConflito', 'idImpactoAmbiental']);

            // Constraints de chave estrangeira
            $table->foreign('idConflito')
                  ->references('idConflito')
                  ->on('conflito')
                  ->onDelete('cascade');

            $table->foreign('idImpactoAmbiental')
                  ->references('idImpactoAmbiental')
                  ->on('impacto_ambiental')
                  ->onDelete('cascade');
        });

        Schema::create('impacto_saude_conflito', function (Blueprint $table) {
            // Chaves estrangeiras
            $table->unsignedBigInteger('idConflito');
            $table->unsignedBigInteger('idImpactoSaude');

            // Chave primária composta
            $table->primary(['idConflito', 'idImpactoSaude']);

            // Constraints de chave estrangeira
            $table->foreign('idConflito')
                  ->references('idConflito')
                  ->on('conflito')
                  ->onDelete('cascade');

            $table->foreign('idImpactoSaude')
                  ->references('idImpactoSaude')
                  ->on('impacto_saude')
                  ->onDelete('cascade');
        });

        Schema::create('impacto_socio_economico_conflito', function (Blueprint $table) {
            // Chaves estrangeiras
            $table->unsignedBigInteger('idConflito');
            $table->unsignedBigInteger('idImpactoSocioEconomico');

            // Chave primária composta
            $table->primary(['idConflito', 'idImpactoSocioEconomico']);

            // Constraints de chave estrangeira
            $table->foreign('idConflito')
                  ->references('idConflito')
                  ->on('conflito')
                  ->onDelete('cascade');

            $table->foreign('idImpactoSocioEconomico')
                  ->references('idImpactoSocioEconomico')
                  ->on('impacto_socio_economico')
                  ->onDelete('cascade');
        });
        
        Schema::create('tipo_conflito', function (Blueprint $table) {
            $table->id('idTipoConflito')->primary();
            $table->string('nome', 100);
        });
        
        Schema::create('assunto', function (Blueprint $table) {
            $table->id('idAssunto')->primary();
            $table->string('nome', 50);
        });
                
        Schema::create('impacto_ambiental', function (Blueprint $table) {
            $table->id('idImpactoAmbiental')->primary();
            $table->string('nome', 50);
        });
                    
        Schema::create('impacto_saude', function (Blueprint $table) {
            $table->id('idImpactoSaude')->primary();
            $table->string('nome', 100);
        });
                        
        Schema::create('impacto_socio_economico', function (Blueprint $table) {
            $table->id('idImpactoSocioEconomico')->primary();
            $table->string('nome', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assunto_conflito');
        Schema::dropIfExists('conflito_tipo_conflito');
        Schema::dropIfExists('impacto_ambiental_conflito');
        Schema::dropIfExists('impacto_saude_conflito');
        Schema::dropIfExists('impacto_socio_economico_conflito');
        Schema::dropIfExists('conflito');
        Schema::dropIfExists('tipo_conflito');
        Schema::dropIfExists('assunto');
        Schema::dropIfExists('impacto_ambiental');
        Schema::dropIfExists('impacto_saude');
        Schema::dropIfExists('impacto_socio_economico');
    }
};
