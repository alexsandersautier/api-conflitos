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
            ['nome' => 'Desmatamento'],
            ['nome' => 'Erosão do solo'],
            ['nome' => 'Incêndios'],
            ['nome' => 'Inundações (rios, costeiras, lama)'],
            ['nome' => 'Perda de agrobiodiversidade'],
            ['nome' => 'Degradação paisagística/estética'],            
            ['nome' => 'Perturbação em grande escala de sistemas hídricos e geológicos'],
            ['nome' => 'Poluição do ar'],
            ['nome' => 'Poluição sonora'],
            ['nome' => 'Outros'],
        ];
        
        DB::table('impacto_ambiental')->insert($impactos);
    }
}