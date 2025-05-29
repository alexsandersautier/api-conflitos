<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoProcessoSeiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipos = [
            ['nome' => 'Tipo de Processo A'],
            ['nome' => 'Tipo de Processo B'],
            ['nome' => 'Tipo de Processo C'],
            ['nome' => 'Tipo de Processo D']
        ];
        
        DB::table('tipo_processo_sei')->insert($tipos);
    }
}