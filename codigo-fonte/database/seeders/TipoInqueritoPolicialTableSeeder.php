<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoInqueritoPolicialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tiposInqueritoPolicials = [
            ['nome' => 'Registro de B.O'], // (opção para inserir dada e nº do BO)
            ['nome' => 'Processos em tramitação na justica estadual'], // (opção para inserir dados possíveis)
            ['nome' => 'Processos em tramitação na justica federal'], // (opção para inserir dados possíveis)
            ['nome' => 'Assistência Jurídica'] // (opção para indicar DPE, DPU, PFE e outros)
        ];
        
        DB::table('tipo_inquerito_policial')->insert($tiposInqueritoPolicials);
    }
}