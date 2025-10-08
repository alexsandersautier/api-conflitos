<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conflito', function (Blueprint $table) {
            $table->id('idConflito')->primary();
            
            // Campos principais
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('nome');
            $table->text('relato');
            $table->date('dataInicioConflito');
            $table->date('dataAcionamentoMpiConflito')->nullable();
            $table->string('regiaoPrioritaria')->nullable();
            $table->string('classificacaoGravidadeConflitoDemed')->nullable();
            $table->string('atualizacaoClassificacaoGravidadeConflito')->nullable();
            $table->date('dataReferenciaMudancaClassificacao')->nullable();
            $table->string('estrategiaGeralUtilizadaDemed')->nullable();
            $table->text('observacoes')->nullable();
            
            // Flags como string
            $table->string('flagHasImpactoAmbiental', 3)->default('NÃO');
            $table->string('flagHasImpactoSaude', 3)->default('NÃO');
            $table->string('flagHasImpactoSocioEconomico', 3)->default('NÃO');
            $table->string('flagHasViolenciaIndigena', 3)->default('NÃO');
            $table->string('flagHasMembroProgramaProtecao', 3)->default('NÃO');
            $table->string('flagHasBOouNF', 3)->default('NÃO');
            $table->string('flagHasInquerito', 3)->default('NÃO');
            $table->string('flagHasProcessoJudicial', 3)->default('NÃO');
            $table->string('flagHasAssistenciaJuridica', 3)->default('NÃO');
            $table->string('flagHasRegiaoPrioritaria', 3)->default('NÃO');
            $table->string('flagHasViolenciaPatrimonialIndigena', 3)->default('NÃO');
            $table->string('flagHasEventoViolenciaIndigena', 3)->default('NÃO');
            $table->string('flagHasAssassinatoPrisaoNaoIndigena', 3)->default('NÃO');
            
            // Programa de proteção
            $table->string('numeroSeiProgramaProtecao')->nullable();
            $table->string('idProgramaProtecao')->nullable();
            $table->string('ufProgramaProtecao', 2)->nullable();
                                    
            // Status
            $table->string('status')->default('EM ANÁLISE');
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('aldeia_conflito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idConflito')->constrained('conflito')->onDelete('cascade');
            $table->foreignId('idAldeia')->constrained('aldeia')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['idConflito', 'idAldeia']);
        });
        
        Schema::create('assunto_conflito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idConflito')->constrained('conflito')->onDelete('cascade');
            $table->foreignId('idAssunto')->constrained('assunto')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['idConflito', 'idAssunto']);
        });
            
        Schema::create('ator_conflito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idConflito')->constrained('conflito')->onDelete('cascade');
            $table->foreignId('idAtor')->constrained('ator')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['idConflito', 'idAtor']);
        });
        
        Schema::create('categoria_ator_conflito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idConflito')->constrained('conflito')->onDelete('cascade');
            $table->foreignId('idCategoriaAtor')->constrained('categoria_ator')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['idConflito', 'idCategoriaAtor']);
        });

        Schema::create('povo_conflito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idConflito')->constrained('conflito')->onDelete('cascade');
            $table->foreignId('idPovo')->constrained('povo')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['idConflito', 'idPovo']);
        });
        
        Schema::create('terra_indigena_conflito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idConflito')->constrained('conflito')->onDelete('cascade');
            $table->foreignId('idTerraIndigena')->constrained('terra_indigena')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['idConflito', 'idTerraIndigena']);
        });

        Schema::create('conflito_tipo_conflito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idConflito')->constrained('conflito')->onDelete('cascade');
            $table->foreignId('idTipoConflito')->constrained('tipo_conflito')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['idConflito', 'idTipoConflito']);
        });

        Schema::create('impacto_ambiental_conflito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idConflito')->constrained('conflito')->onDelete('cascade');
            $table->foreignId('idImpactoAmbiental')->constrained('impacto_ambiental')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['idConflito', 'idImpactoAmbiental']);
        });

        Schema::create('impacto_saude_conflito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idConflito')->constrained('conflito')->onDelete('cascade');
            $table->foreignId('idImpactoSaude')->constrained('impacto_saude')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['idConflito', 'idImpactoSaude']);
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
            $table->timestamps();
        });
        
        Schema::create('tipo_conflito', function (Blueprint $table) {
            $table->id('idTipoConflito')->primary();
            $table->string('nome', 100);
        });
        
        Schema::create('assunto', function (Blueprint $table) {
            $table->id('idAssunto')->primary();
            $table->string('nome', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assunto');
        Schema::dropIfExists('aldeia_conflito');
        Schema::dropIfExists('assunto_conflito');
        Schema::dropIfExists('ator_conflito');
        Schema::dropIfExists('categoria_ator_conflito');
        Schema::dropIfExists('conflito_tipo_conflito');
        Schema::dropIfExists('impacto_ambiental_conflito');
        Schema::dropIfExists('impacto_saude_conflito');
        Schema::dropIfExists('impacto_socio_economico_conflito');
        Schema::dropIfExists('conflito');
        Schema::dropIfExists('tipo_conflito');
        Schema::dropIfExists('povo_conflito');
        Schema::dropIfExists('terra_indigena_conflito');
    }
};
