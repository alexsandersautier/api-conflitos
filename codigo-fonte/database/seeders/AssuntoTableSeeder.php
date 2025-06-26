<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssuntoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assuntos = [
            ['nome' => 'Retomada de terras'],
            ['nome' => 'Processo demarcatório'],
            ['nome' => 'Grilagem'],
            ['nome' => 'Desmatamento'],
            ['nome' => 'Exploração de recurso florestal e/ou faunístico'],
            ['nome' => 'Atividade minerária'],
            ['nome' => 'Arrendamento de terras'],
            ['nome' => 'Contaminação por agrotóxico'],
            ['nome' => 'Impacto ambiental'],
            ['nome' => 'Empreendimento de infraestrutura'],
            ['nome' => 'Empreendimento turístico e/ou imobiliário'],
            ['nome' => 'Crime organizado'],
            ['nome' => 'Sobreposição com unidade de conservação'],
            ['nome' => 'Sobreposição com território quilombola'],
            ['nome' => 'Sobreposição com projeto de assentamento'],
            ['nome' => 'Sobreposição entre territórios indígenas'],
            ['nome' => 'Disputa interna'],
            ['nome' => 'Violência contra pessoas e coletividades (assunto)'],
            ['nome' => 'Ameaça, agressão e assassinato de indígena'],
            ['nome' => 'Desaparecimento de indígena'],
            ['nome' => 'Violência contra a mulher'],
            ['nome' => 'Violência policial'],
            ['nome' => 'Violência paramilitar'],
            ['nome' => 'Criminalização de liderança'],
            ['nome' => 'Violência contra servidor público'],
            ['nome' => 'Produção de dados e monitoramento de conflito'],
            ['nome' => 'Formação para a prevenção de conflito'],
            ['nome' => 'Articulação interinstitucional'],
            ['nome' => 'Racismo religioso'],
            ['nome' => 'Queima e/ou destruição de espaços de artefatos religiosos'],
            ['nome' => 'Despejo'],
            ['nome' => 'Tráfico'],
            ['nome' => 'Racismo religioso'],
            ['nome' => 'Destruição de espaços e/ou artefatos religiosos'],
            ['nome' => 'Desaparecimento de membros da comunidade']
        ];
        
        DB::table('assunto')->insert($assuntos);
    }
}