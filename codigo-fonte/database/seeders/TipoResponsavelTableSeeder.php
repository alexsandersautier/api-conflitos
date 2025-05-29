<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoResponsavelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['nome' => 'Denúncia feita pela comunidade indígena'],
            ['nome' => 'Denúncia feita por organização indígena e/ou indigenista'],
            ['nome' => 'Denúncia anônima na Ouvidoria'],
            ['nome' => 'Acionamento por terceiros'],
            ['nome' => 'Acionamento pela FUNAI'],
            ['nome' => 'Acionamento pelo MPF'],
            ['nome' => 'Outros Site de notícia'],
            ['nome' => 'Instagram'],
            ['nome' => 'Facebook'],
            ['nome' => 'Blog de ONG'],
            ['nome' => 'Blog de pessoa pública'],
            ['nome' => 'Denúncia anônima'],
            ['nome' => 'Pesquisa IEB']
        ];
        
        DB::table('tipo_responsavel')->insert($tipos);
    }
}
