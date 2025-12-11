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
        // Schema::create('sobreposicao_territorial', function (Blueprint $table) {
        // $table->id('idSobreposicaoTerritotial')->primary();
        // $table->string('sobreposicao_unidade_conservacao', 200);
        // $table->string('nome', 200);
        // $table->decimal('area', 10, 2);

        // $table->decimal('area_sobreposicao_tis_elencadas', 10, 2);
        // $table->string('sobreposicao_assentamento', 200); // (federal, estadual e municipal)
        // $table->string('nome_assentamento', 200);
        // $table->string('area_assentamento', 200);

        // $table->string('area_sobreposicao_tis_elencadas', 200);
        // $table->string('sobreposicao_imoveis_publicos_georreferenciamento_certificado', 200);
        // $table->string('nome_imovel_publico', 200);
        // $table->string('cod_imovel', 200);
        // $table->string('area_imovel_publico', 200);

        // $table->string('area_sobreposicao_tis_elencadas', 200);
        // $table->string('sobreposicao_imoveis_privados_georreferenciamento_certificado', 200);
        // $table->string('Nome do imóvel privado', 200);
        // $table->string('cod_imovel', 200);
        // $table->string('area_imovel_privado', 200);

        // $table->string('area_sobreposicao_tis_elencadas', 200);
        // $table->string('sobreposicao_areas_inscritas_car', 200);
        // $table->string('cod_area', 200);
        // $table->string('area_poligono', 200);

        // $table->string('area_sobreposicao_tis_elencadas', 200);
        // $table->string('nome_imovel', 200);
        // $table->string('area_imovel', 200);

        // $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('sobreposicao_territorial');
    }
};
