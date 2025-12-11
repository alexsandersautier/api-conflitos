<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {

    public function up()
    {
        Schema::create('aldeia', function (Blueprint $table) {
            $table->id('idAldeia');

            // Dados de Localização Geográfica
            $table->string('cd_uf', 2); // Código UF
            $table->string('nm_uf'); // Nome UF
            $table->string('cd_munic', 7); // Código Município
            $table->string('nm_munic'); // Nome Município
            $table->string('id_li')->unique(); // ID Localidade Indígena
            $table->string('cd_li'); // Código Localidade Indígena
            $table->string('ocorrencia')->nullable(); // Ocorrência
            $table->string('nome'); // Nome Aldeia
            $table->string('cd_setor')->nullable(); // Código Setor
            $table->string('situacao')->nullable(); // Situação
            $table->string('cd_sit')->nullable(); // Código Situação
            $table->string('cd_tipo')->nullable(); // Código Tipo
            $table->string('cd_aglom')->nullable(); // Código Aglomeração
            $table->string('nm_aglom')->nullable(); // Nome Aglomeração

            // Dados de Terra Indígena
            $table->string('cd_ti')->nullable(); // Código TI
            $table->string('ti_funai')->nullable(); // TI FUNAI
            $table->string('nm_ti')->nullable(); // Nome TI
            $table->string('fase')->nullable(); // Fase
            $table->string('c_cr_funai')->nullable(); // Código CR FUNAI
            $table->string('n_cr_funai')->nullable(); // Nome CR FUNAI

            // Dados de Aldeias
            $table->string('aldeia_funai')->nullable(); // Aldeia FUNAI
            $table->string('val_funai')->nullable(); // Valor FUNAI
            $table->string('aldeia_siasi')->nullable(); // Aldeia SIASI
            $table->string('val_siasi')->nullable(); // Valor SIASI

            // Dados Geográficos
            $table->string('amz_leg')->nullable(); // Amazônia Legal
            $table->decimal('lat', 10, 8)->nullable(); // Latitude
            $table->decimal('long', 11, 8)->nullable(); // Longitude

            $table->timestamps();
            $table->softDeletes();

            // Índices para melhor performance
            $table->index('cd_uf');
            $table->index('cd_munic');
            $table->index('cd_li');
            $table->index('cd_ti');
            $table->index('nome');
            $table->index('nm_uf');
            $table->index('nm_munic');
        });
    }

    public function down()
    {
        Schema::dropIfExists('aldeia');
    }
};