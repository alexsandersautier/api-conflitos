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
            ['nome' => 'Insegurança alimentar'],
            ['nome' => 'Aliciamento e cooptação de atores dos povos indígenas'],
            ['nome' => 'Criminalização da população indígena'],
            ['nome' => 'Expropriação territorial'],
            ['nome' => 'Deslocamento forçado'],
            ['nome' => 'Militarização e aumento da presença policial'],
            ['nome' => 'Perda de conhecimentos socioculturais'],
            ['nome' => 'Perda de meios de subsistência'],
            ['nome' => 'Perda de vínculos laborais'],
            ['nome' => 'Afetação na prestação de serviços básicos'],
            ['nome' => 'Aumento da violência e psicológica'],
            ['nome' => 'Aumento da violência contra a mulher'],
            ['nome' => 'Aumento da violência contra criança e adolescentes']
        ];
            
        DB::table('impacto_socio_economico')->insert($impactos);
    }
}