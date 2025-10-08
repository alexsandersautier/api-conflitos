<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConflitoValidationTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_validacao_flags_sim_nao()
    {
        $payload = [
            "latitude" => "-16.762467717941604",
            "longitude" => "-54.3218994140625",
            "nome" => "Conflito Validação Flags",
            "relato" => "Relato do conflito",
            "dataInicioConflito" => "2025-10-07",
            "dataAcionamentoMpiConflito" => "2025-10-23",
            "regiao" => "Centro-Oeste",
            "uf" => "GO",
            "municipio" => "Águas Lindas de Goiás",
            "flagHasImpactoAmbiental" => "INVALIDO", // Valor inválido
            "flagHasImpactoSaude" => "SIM",
            "flagHasImpactoSocioEconomico" => "NÃO",
            "flagHasViolenciaIndigena" => "SIM",
            "flagHasMembroProgramaProtecao" => "NÃO",
            "flagHasBOouNF" => "NÃO",
            "flagHasInquerito" => "NÃO",
            "flagHasProcessoJudicial" => "NÃO",
            "flagHasAssistenciaJuridica" => "NÃO",
            "flagHasRegiaoPrioritaria" => "NÃO",
            "flagHasViolenciaPatrimonialIndigena" => "NÃO",
            "flagHasEventoViolenciaIndigena" => "NÃO",
            "flagHasAssassinatoPrisaoNaoIndigena" => "NÃO",
        ];
        
        $response = $this->postJson('/api/conflitos', $payload);
        
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['flagHasImpactoAmbiental']);
    }
    
    public function test_validacao_datas()
    {
        $payload = [
            "latitude" => "-16.762467717941604",
            "longitude" => "-54.3218994140625",
            "nome" => "Conflito Validação Datas",
            "relato" => "Relato do conflito",
            "dataInicioConflito" => "2025-10-07",
            "dataAcionamentoMpiConflito" => "2025-09-01", // Data anterior ao início do conflito
            "regiao" => "Centro-Oeste",
            "uf" => "GO",
            "municipio" => "Águas Lindas de Goiás",
            "flagHasImpactoAmbiental" => "SIM",
            "flagHasImpactoSaude" => "SIM",
            "flagHasImpactoSocioEconomico" => "NÃO",
            "flagHasViolenciaIndigena" => "SIM",
            "flagHasMembroProgramaProtecao" => "NÃO",
            "flagHasBOouNF" => "NÃO",
            "flagHasInquerito" => "NÃO",
            "flagHasProcessoJudicial" => "NÃO",
            "flagHasAssistenciaJuridica" => "NÃO",
            "flagHasRegiaoPrioritaria" => "NÃO",
            "flagHasViolenciaPatrimonialIndigena" => "NÃO",
            "flagHasEventoViolenciaIndigena" => "NÃO",
            "flagHasAssassinatoPrisaoNaoIndigena" => "NÃO",
        ];
        
        $response = $this->postJson('/api/conflitos', $payload);
        
        // A validação de datas deve passar mesmo com data anterior
        // pois não há regra de negócio que impeça isso
        $response->assertStatus(201);
    }
    
    public function test_validacao_uf()
    {
        $payload = [
            "latitude" => "-16.762467717941604",
            "longitude" => "-54.3218994140625",
            "nome" => "Conflito Validação UF",
            "relato" => "Relato do conflito",
            "dataInicioConflito" => "2025-10-07",
            "dataAcionamentoMpiConflito" => "2025-10-23",
            "regiao" => "Centro-Oeste",
            "uf" => "G", // UF inválida (apenas 1 caractere)
            "municipio" => "Águas Lindas de Goiás",
            "flagHasImpactoAmbiental" => "SIM",
            "flagHasImpactoSaude" => "SIM",
            "flagHasImpactoSocioEconomico" => "NÃO",
            "flagHasViolenciaIndigena" => "SIM",
            "flagHasMembroProgramaProtecao" => "NÃO",
            "flagHasBOouNF" => "NÃO",
            "flagHasInquerito" => "NÃO",
            "flagHasProcessoJudicial" => "NÃO",
            "flagHasAssistenciaJuridica" => "NÃO",
            "flagHasRegiaoPrioritaria" => "NÃO",
            "flagHasViolenciaPatrimonialIndigena" => "NÃO",
            "flagHasEventoViolenciaIndigena" => "NÃO",
            "flagHasAssassinatoPrisaoNaoIndigena" => "NÃO",
        ];
        
        $response = $this->postJson('/api/conflitos', $payload);
        
        $response->assertStatus(422)
        ->assertJsonValidationErrors(['uf']);
    }
}