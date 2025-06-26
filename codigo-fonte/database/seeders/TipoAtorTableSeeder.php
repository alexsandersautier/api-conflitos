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
            ['nome' => 'Agentes de segurança pública'],
            ['nome' => 'Detentor de pequena propriedade rural'],
            ['nome' => 'Detentor de média ou grande propriedade rural'],
            ['nome' => 'Garimpeiro'],
            ['nome' => 'Indústrias de Mineração'],
            ['nome' => 'Indústrias madereira'],
            ['nome' => 'Madereiros ilegais'],
            ['nome' => 'Indústrias de energia'],
            ['nome' => 'Indústrias de exploração de combustível fóssil'],
            ['nome' => 'Empreendimento de infraestrutura'],
            ['nome' => 'Setor público']
        ];
        
        DB::table('tipo_ator')->insert($tipos);
    }
}