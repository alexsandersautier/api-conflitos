<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAtorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipos = [
            ['nome' => 'Fazendeiro'],
            ['nome' => 'Garimpeiro'],
            ['nome' => 'Grileiro'],
            ['nome' => 'Indústrias de Mineração'],
            ['nome' => 'Indústrias de energia e exploração de combustível fóssil'],
            ['nome' => 'Indústrias outras (Grande, Médio e Pequena)'],
            ['nome' => 'Latifundiário'],
            ['nome' => 'Madeireiro'],
            ['nome' => 'Setor público (obras públicas)'],
            ['nome' => 'Agentes de segurança pública'],
        ];
        
        DB::table('tipo_ator')->insert($tipos);
    }
}