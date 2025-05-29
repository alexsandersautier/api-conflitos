<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImpactoAmbientalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $impactos = [
            ['nome' => 'Contaminação de cursos hídricos'],
            ['nome' => 'Contaminação do solo'],
            ['nome' => 'Desertificação/seca'],
            ['nome' => 'Derramamento de dejetos'],
            ['nome' => 'Desmatamento sem manejo'],
            ['nome' => 'Erosão do solo'],
            ['nome' => 'Incêndios'],
            ['nome' => 'Insegurança alimentar (danos ao seu modo de vida)'],
            ['nome' => 'Inundações (rios, costeiras, lama)'],
            ['nome' => 'Perda de biodiversidade (vida selvagem, agrobiodiversidade)'],
            ['nome' => 'Perda de degradação paisagística/estética'],
            ['nome' => 'Perturbação em grande escala de sistemas hídricos e geológicos'],
            ['nome' => 'Poluição das águas superficiais'],
            ['nome' => 'Poluição do ar'],
            ['nome' => 'Poluição ou esgotamento das águas subterrâneas'],
            ['nome' => 'Poluição sonora'],
            ['nome' => 'Seca dos cursos hídricos'],
            ['nome' => 'Vazamento de dejetos'],
            ['nome' => 'Outros'],
        ];
        
        DB::table('impacto_ambiental')->insert($impactos);
    }
}