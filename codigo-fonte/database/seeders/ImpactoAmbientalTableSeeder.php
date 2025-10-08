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
            ['nome' => "Desvio de curso d'água"],
            ['nome' => 'Assoreamento de rio'],
            ['nome' => 'Perturbação em grande escala de sistemas hídricos e geológicos'],
            ['nome' => 'Inundações/Alagamentos (rios, costeiras, lama)'],
            ['nome' => 'Contaminação do solo'],
            ['nome' => 'Contaminação por resíduos de agrotóxicos'],
            ['nome' => 'Erosão do solo'],
            ['nome' => 'Desertificação/Seca'],
            ['nome' => 'Incêndios'],
            ['nome' => 'Desmatamento por desfolhante'],
            ['nome' => 'Desmatamento'],
            ['nome' => 'Degradação ambiental por exploração de madeira'],
            ['nome' => 'Devastação ligada a agronegócio'],
            ['nome' => 'Perda de agrobiodiversidade'],
            ['nome' => 'Caça, venda ilegal e ameaça à biodiversidade'],
            ['nome' => 'Impactos na Fauna e Flora'],
            ['nome' => 'Degradação paisagística/estética'],
            ['nome' => 'Poluição do ar'],
            ['nome' => 'Poluição sonora']
        ];
        
        DB::table('impacto_ambiental')->insert($impactos);
    }
}