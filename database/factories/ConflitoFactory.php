<?php

namespace Database\Factories;

use App\Models\Conflito;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TerraIndigena;
use App\Models\Povo;

class ConflitoFactory extends Factory
{
    protected $model = Conflito::class;
    
    public function definition()
    {        
        static $terraIndigenaIds = null;
        static $povoIds = null;
        
        if (is_null($terraIndigenaIds)) {
            $terraIndigenaIds = TerraIndigena::pluck('idTerraIndigena')->toArray();
            if (empty($terraIndigenaIds)) {
                $terraIndigenaIds = TerraIndigena::factory()->count(10)->create()->pluck('idTerraIndigena')->toArray();
            }
        }
        
        if (is_null($povoIds)) {
            $povoIds = Povo::pluck('idPovo')->toArray();
            if (empty($povoIds)) {
                $povoIds = Povo::factory()->count(5)->create()->pluck('idPovo')->toArray();
            }
        }
        
        // Coordenadas do Brasil com margem de segurança
        $brasilBounds = [ 'latitude' => [
                                'min' => -9.362352822055593,
                                'max' => -0.4394488164139641
                            ],
                          'longitude' => [
                              'min' => -65.43457031250001,
                              'max' => -47.94433593750001
                            ]
                        ];
        
        $regioes = ['Norte', 'Nordeste', 'Centro-Oeste', 'Sudeste', 'Sul'];
        
        $ufs['Norte']        = ['AC', 'AM', 'PA', 'RO', 'RR', 'AP', 'TO'];
        $ufs['Nordeste']     = ['MA', 'PI', 'CE', 'RN', 'PB', 'PE', 'AL', 'SE', 'BA'];
        $ufs['Sudeste']      = ['MG', 'ES', 'RJ', 'SP'];
        $ufs['Sul']          = ['PR', 'SC', 'RS']; 
        $ufs['Centro-Oeste'] = ['MS', 'MT', 'GO', 'DF'];
        

        $idTerraIndigena = $this->faker->randomElement($terraIndigenaIds);
        $idPovo          = $this->faker->randomElement($povoIds);
        
//         $idTerraIndigena = $this->faker->randomElement($terraIndigenaRandom);
//         $idPovo          = $this->faker->randomElement($povoRandom);
        
        //echo "TerraIndigena ................. $idTerraIndigena \n";
        //echo "Povo ................. $idPovo \n";
        //echo "=====================================================\n";
        $regiao = $this->faker->randomElement($regioes);
        $uf     = $this->faker->randomElement($ufs[$regiao]);
        
        return [
            'idTerraIndigena' => $idTerraIndigena,
            
            'idPovo' => $idPovo,
            
            'nome' => $this->faker->sentence(3),
            'descricao' => $this->faker->paragraph(3),
            'regiao' => $this->faker->randomElement($regioes),
            'dataInicioConflito' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'dataFimConflito' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'latitude' => $this->faker->latitude($brasilBounds['latitude']['min'],
                                                 $brasilBounds['latitude']['max']),
            'longitude' => $this->faker->longitude($brasilBounds['longitude']['min'],
                                                   $brasilBounds['longitude']['max']),
            'municipio' => $this->faker->city,
            'uf' => $uf,
            'flagOcorrenciaAmeaca' => $this->faker->boolean(30),
            'flagOcorrenciaViolencia' => $this->faker->boolean(20),
            'flagOcorrenciaAssassinato' => $this->faker->boolean(10),
            'flagOcorrenciaFeridos' => $this->faker->boolean(15),
            'flagMembroProgramaProtecao' => $this->faker->boolean(25),
        ];
    }
}