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
            ['nome' => 'Aliciamento e cooptação da população indígena'],
            ['nome' => 'Aumento de violência física e psicológica nas relações internas'],
            ['nome' => 'Aumento da violência contra a mulher'],
            ['nome' => 'Aumento da violência contra criança e adolescente'],
            ['nome' => 'Aumento da violência contra pessoas LGBTQIA+'],
            ['nome' => 'Criminalização da população indígena'],
            ['nome' => 'Danos materiais à moradia'],
            ['nome' => 'Deslocamento forçado'],
            ['nome' => 'Expropriação territorial'],
            ['nome' => 'Falta de acesso a benefícios previdenciário e sociais'],
            ['nome' => 'Insegurança alimentar'],
            ['nome' => 'Militarização e aumento da presença policial'],
            ['nome' => 'Precariedade de moradia'],
            ['nome' => 'Perda de conhecimentos socioculturais'],
            ['nome' => 'Perda de meios de subsistência'],
            ['nome' => 'Perda de vínculos laborais'],
            ['nome' => 'Privação de serviços públicos básicos']
        ];
            
        DB::table('impacto_socio_economico')->insert($impactos);
    }
}