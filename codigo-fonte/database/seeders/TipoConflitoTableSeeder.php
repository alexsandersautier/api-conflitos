<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoConflitoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tiposConflitos = [
            ['nome' => 'Prevenção e acompanhamento de conflito: Disputas territoriais'],
            ['nome' => 'Prevenção e acompanhamento de conflito: Violência contra pessoas e coletividades'],
            ['nome' => 'Prevenção e acompanhamento de conflito: Prevenção de conflito'],
        ];
        
        DB::table('tipo_conflito')->insert($tiposConflitos);
    }
}