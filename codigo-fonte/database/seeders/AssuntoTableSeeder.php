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
            ['nome' => 'Empreendimento de infraestrutura'],
            ['nome' => 'Empreendimento turístico e/ou imobiliário'],
            ['nome' => 'Crime organizado'],
            ['nome' => 'Sobreposição com unidade de conservação'],
            ['nome' => 'Sobreposição com território quilombola'],
            ['nome' => 'Sobreposição com projeto de assentamento'],
            ['nome' => 'Disputa interna'],
            ['nome' => 'Ameaça, agressão e assassinato de indígena'],
            ['nome' => 'Violência policial'],
            ['nome' => 'Violência paramilitar'],
            ['nome' => 'Criminalização de liderança'],
            ['nome' => 'Violência contra servidor público'],
            ['nome' => 'Produção de dados e monitoramento de conflito'],
            ['nome' => 'Formação para a prevenção de conflito'],
            ['nome' => 'Articulação interinstitucional'],
            ['nome' => 'Racismo religioso'],
            ['nome' => 'Queima e/ou destruição de espaços de artefatos religiosos'],
            ['nome' => 'Despejo (com possibilidade de qualificar)'],
            ['nome' => 'Tráfico (com possibilidade de qualificar)'],
            ['nome' => 'Desaparecimento de membros da comunidade'],
            ['nome' => 'Outros'],
        ];
        
        DB::table('assunto')->insert($assuntos);
    }
}