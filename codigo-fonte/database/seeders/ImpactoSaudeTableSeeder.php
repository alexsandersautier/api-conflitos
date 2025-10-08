<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImpactoSaudeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * 
    */
    public function run()
    {
        $impactosSaude = [
            ['nome' => 'Lesões por acidentes'],
            ['nome' => 'Doenças infecciosas'],
            ['nome' => 'Doenças relacionadas ao meio ambiente contaminado, incluindo a falta de saneamenento básico (diarreia, vômito, mal-estar, doenças de pele, entre outros)'],
            ['nome' => 'Doenças relacionadas à saúde mental (uso problemático de álcool e outras drogas ilícitas, ansiedade, depressão, estresse, suicídio, Transtorno de Estresse Pós-traumático (TEPT), entre outros)'],
            ['nome' => 'Doenças devido a privação de recursos (desnutrição e subnutrição)'],
            ['nome' => 'Contaminação por COVID-19'],
            ['nome' => 'Lesões por agressão']
        ];
                    
        DB::table('impacto_saude')->insert($impactosSaude);
    }
}