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
        Schema::create('terra_indigena', function (Blueprint $table) {            
            $table->id('idTerraIndigena')->primary();
            $table->foreignId('idPovo')->constrained('povo');
            $table->foreignId('idSituacaoFundiaria')->constrained('situacao_fundiaria');
            $table->integer('codigo_ti');
            $table->string('nome', 50);
            $table->string('superficie_perimetro_ha', 200);
            $table->string('modalidade_ti', 200);
            $table->string('etnia_nome', 200);
            $table->string('municipio_nome', 200);
            $table->string('uf_sigla', 200);
            $table->string('coordenacao_regional', 200);
            $table->boolean('faixa_fronteira');
            $table->string('undadm_codigo', 200);
            $table->string('undadm_nome', 200);
            $table->string('undadm_sigla', 50);
            $table->date('data_atualizacao')->nullable();
            $table->date('data_homologacao')->nullable();
            $table->string('decreto_homologacao', 50)->nullable();
            $table->date('data_regularizacao')->nullable();
            $table->string('matricula_regularizacao', 50)->nullable();
            $table->string('acao_recuperacao_territorial', 50)->nullable();
            $table->string('dominio_uniao', 1)->nullable();
            $table->string('numero_processo_funai', 50)->nullable();
            $table->date('data_abertura_processo_funai')->nullable();
            $table->string('numero_portaria_funai', 50)->nullable();
            $table->string('numero_processo_sei', 50)->nullable();
            $table->string('numero_portaria_declaratoria', 50)->nullable();
            $table->integer('qtd_aldeias')->nullable();
            $table->integer('qtd_familias')->nullable();
            $table->longText('links_documentos_vinculados')->nullable();
            $table->timestamps();
        });

        Schema::create('situacao_fundiaria', function (Blueprint $table) {
            $table->id('idSituacaoFundiaria')->primary();
            $table->string('nome', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terra_indigena');
        Schema::dropIfExists('situacao_fundiaria');
    }
};
