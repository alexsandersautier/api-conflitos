<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImpactoSocioEconomicoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $impactos = [
            ['nome' => 'Aliciamento e cooptação de atores dos povos indígenas'],
            ['nome' => 'Aumento da violência e criminalidade'],
            ['nome' => 'Desapropriação de terras'],
            ['nome' => 'Deslocamento compulsório'],
            ['nome' => 'Militarização e aumento da presença policial'],
            ['nome' => 'Perda de conhecimentos socioculturais'],
            ['nome' => 'Perda de meios de subsistência'],
            ['nome' => 'Violações dos direitos humanos'],
            ['nome' => 'Outros'],
        ];
            
        DB::table('impacto_socio_economico')->insert($impactos);
    }
}