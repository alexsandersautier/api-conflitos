<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SituacaoFundiariaTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $situacoes = [
            [
                'nome' => 'Em Estudo'
            ],
            [
                'nome' => 'Delimitada'
            ],
            [
                'nome' => 'Declarada'
            ],
            [
                'nome' => 'Homologada'
            ],
            [
                'nome' => 'Regularizada'
            ],
            [
                'nome' => 'Encaminhada RI'
            ]
        ];

        DB::table('situacao_fundiaria')->insert($situacoes);
    }
}
