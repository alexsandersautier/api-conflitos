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
            ['nome' => 'RETOMADA DE TERRAS'],
            ['nome' => 'PROCESSO DEMARCATÓRIO'],
            ['nome' => 'GRILAGEM'],
            ['nome' => 'DESMATAMENTO'],
            ['nome' => 'EXPLORAÇÃO DE RECURSO FLORESTAL E/OU FAUNÍSTICO'],
            ['nome' => 'ATIVIDADE MINERÁRIA'],
            ['nome' => 'ARRENDAMENTO DE TERRAS'],
            ['nome' => 'CONTAMINAÇÃO POR AGROTÓXICO'],
            ['nome' => 'IMPACTO AMBIENTAL'],
            ['nome' => 'EMPREENDIMENTO DE INFRAESTRUTURA'],
            ['nome' => 'EMPREENDIMENTO TURÍSTICO E/OU IMOBILIÁRIO'],
            ['nome' => 'CRIME ORGANIZADO'],
            ['nome' => 'SOBREPOSIÇÃO COM UNIDADE DE CONSERVAÇÃO'],
            ['nome' => 'SOBREPOSIÇÃO COM TERRITÓRIO QUILOMBOLA'],
            ['nome' => 'SOBREPOSIÇÃO COM PROJETO DE ASSENTAMENTO'],
            ['nome' => 'SOBREPOSIÇÃO ENTRE TERRITÓRIOS INDÍGENAS'],
            ['nome' => 'DISPUTA INTERNA'],
            ['nome' => 'EXPLORAÇÃO DE SERVIÇOS AMBIENTAIS E ECOSSISTÊMICOS'],
            ['nome' => 'PROJETOS RELACIONADOS AO MERCADO DE CARBONO'],
            ['nome' => 'AMEAÇA, AGRESSÃO E ASSASSINATO DE INDÍGENA'],
            ['nome' => 'DESAPARECIMENTO DE INDÍGENA'],
            ['nome' => 'VIOLÊNCIA CONTRA A MULHER'],
            ['nome' => 'VIOLÊNCIA POLICIAL'],
            ['nome' => 'VIOLÊNCIA PARAMILITAR'],
            ['nome' => 'CRIMINALIZAÇÃO DE LIDERANÇA'],
            ['nome' => 'DESPEJO'],
            ['nome' => 'VIOLÊNCIA CONTRA SERVIDOR PÚBLICO'],
            ['nome' => 'RACISMO'],
            ['nome' => 'DESTRUIÇÃO DE ESPAÇOS E/OU ARTEFATOS RELIGIOSOS'],
            ['nome' => 'VIOLÊNCIA CONTRA PESSOAS LGBTQIA+'],
            ['nome' => 'SUICÍDIO']
        ];
        
        DB::table('assunto')->insert($assuntos);
    }
}