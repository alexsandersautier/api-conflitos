<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AtorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $atores = [
            ['nome' => 'João da Silva'],
            ['nome' => 'Manuel Joaquim'],
            ['nome' => 'Maria de Fátima']
        ];
        
        DB::table('ator')->insert($atores);
    }
}