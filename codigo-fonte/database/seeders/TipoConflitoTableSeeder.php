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
            [
                'nome' => 'Disputas territoriais'
            ],
            [
                'nome' => 'Violência contra pessoas e coletividades'
            ]
        ];

        DB::table('tipo_conflito')->insert($tiposConflitos);
    }
}