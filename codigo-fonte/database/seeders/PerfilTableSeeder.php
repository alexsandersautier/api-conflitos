<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $perfis = [
            ['nome' => 'ADMINISTRADOR'],
            ['nome' => 'SERVIDOR'],
            ['nome' => 'COLABORADOR']
        ];

        DB::table('perfil')->insert($perfis);
    }
}
