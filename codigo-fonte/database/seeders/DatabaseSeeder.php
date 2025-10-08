<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        // User::factory(10)->create();

        Usuario::factory()->create(['nome' => 'Luiz Leão',
                                    'email' => 'luizleao@gmail.com',
                                    'senha' => Hash::make('senha'),
                                    'idOrgao' => 2,
                                    'idPerfil' => 1]);
        
        Usuario::factory()->create(['nome' => 'Maite Alves Guedes',
                                    'email' => 'maite.guedes@povosindigenas.gov.br',
                                    'senha' => Hash::make('senha123456'),
                                    'idOrgao' => 1,
                                    'idPerfil' => 1]);
        
        Usuario::factory()->create(['nome' => 'Paulo Dias',
                                    'email' => 'paulo.dias@iieb.com.br',
                                    'senha' => Hash::make('senha123456'),
                                    'idOrgao' => 3,
                                    'idPerfil' => 1]);
        
        Usuario::factory()->create(['nome' => 'Lais Souza',
                                    'email' => 'lais-l.souza@povosindigenas.gov.br',
                                    'senha' => Hash::make('senha123456'),
                                    'idOrgao' => 3,
                                    'idPerfil' => 1]);
        
        
        
        $this->call([
                    AssuntoTableSeeder::class,
                    AtorTableSeeder::class,
                    AldeiaTableSeeder::class,
                    ImpactoAmbientalTableSeeder::class,
                    ImpactoSaudeTableSeeder::class,
                    ImpactoSocioEconomicoTableSeeder::class,
                    OrgaoTableSeeder::class,
                    SituacaoFundiariaTableSeeder::class,
                    CategoriaAtorTableSeeder::class,
                    TipoConflitoTableSeeder::class,
                    PovoTableSeeder::class,
                    PerfilTableSeeder::class,
                    TerraIndigenaTableSeeder::class,
                    ConflitoTableSeeder::class
                ]);

    }
}
