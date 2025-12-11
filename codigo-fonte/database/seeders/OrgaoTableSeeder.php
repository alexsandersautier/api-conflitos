<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrgaoTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orgaos = [
            [
                'nome' => 'MPI'
            ],
            [
                'nome' => 'PF'
            ],
            [
                'nome' => 'FUNAI'
            ],
            [
                'nome' => 'IBAMA'
            ]
        ];

        DB::table('orgao')->insert($orgaos);
    }
}
