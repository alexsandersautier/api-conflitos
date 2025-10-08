<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaAtorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipos = [
            ['nome' => 'Agricultor familiar'],
            ['nome' => 'Fazendeiro'],
            ['nome' => 'Grileiro'],
            ['nome' => 'Garimpeiro'],
            ['nome' => 'Caçador'],
            ['nome' => 'Empresário'],
            ['nome' => 'Empresa de mineração'],
            ['nome' => 'Empresa madeireira'],
            ['nome' => 'Empresa de produção de energia elétrica'],
            ['nome' => 'Empresa de exploração de combustível fóssil'],
            ['nome' => 'Empresa de turismo'],
            ['nome' => 'Empresa imobiliária'],
            ['nome' => 'Empresa de infraestrutura'],
            ['nome' => 'Empresa relacionada ao mercado de carbono'],
            ['nome' => 'Empresa agrícola'],
            ['nome' => 'Governo Federal'],
            ['nome' => 'Governo Estadual'],
            ['nome' => 'Governo Municipal'],
            ['nome' => 'Agentes de segurança pública'],
            ['nome' => 'Crime organizado'],
            ['nome' => 'Piratas'],
            ['nome' => 'Quilombolas'],
            ['nome' => 'Outros povos indígenas'],
            ['nome' => 'Povos e comunidades tradicionais (exceto quilombolas e indígenas)'],
            ['nome' => 'Parlamentares'],
            ['nome' => 'Pesquisador(a)'],
            ['nome' => 'Organizações da sociedade civil'],
            ['nome' => 'Instituições vinculadas a igrejas'],
            ['nome' => 'Movimento Invasão Zero (MIZ)'],
            ['nome' => 'Servidor público']
        ];
        
        DB::table('categoria_ator')->insert($tipos);
    }
}