<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoEstrategiaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos_estrategia = [
            ['nome' => 'Envio de ofício'],
            ['nome' => 'Realização de reuniões pontuais com os atores envolvidos'],
            ['nome' => 'Realização de escuta qualificada no território'],
            ['nome' => 'Acompanhamento do conflito com ações complexas'],
            ['nome' => 'Construção de estratégias a partir de espaço de articulação inteinstitucional']
        ];
        DB::table('tipo_estrategia')->insert($tipos_estrategia);
    }
}
