<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conflito;

class ConflitoTableSeeder extends Seeder
{

    public function run()
    {
        // Cria 50 registros de conflitos fictícios
        Conflito::factory()->count(50)->create();
    }
}