<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConflitoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_conflito_com_dados_completos()
    {
        // Preparar dados de teste para entidades relacionadas
        $this->criarEntidadesRelacionadas();

        $payload = [
            "numerosSeiIdentificacaoConflito" => [
                "50046.546546/5465-46",
                "50303.232652/2016-81"
            ],
            "violenciasPatrimoniais" => [
                [
                    "id" => 1759423644092,
                    "tipoViolencia" => "Expulsão",
                    "data" => "2025-10-02",
                    "numeroSei" => "50306.336643/2023-11"
                ]
            ],
            "violenciasPessoasIndigenas" => [
                [
                    "id" => 1759423747467,
                    "tipoViolencia" => "Prisão de indígena",
                    "data" => "2025-10-02",
                    "nome" => "Anauê",
                    "idade" => "30",
                    "faixaEtaria" => "Adulto (25-59 anos)",
                    "genero" => "Homem",
                    "instrumentoViolencia" => "Projétil não-letal (balas de borracha, bomba de efeito moral)",
                    "numeroSei" => "50360.032322/2013-32"
                ]
            ],
            "violenciasPessoasNaoIndigenas" => [],
            "latitude" => "-16.762467717941604",
            "longitude" => "-54.3218994140625",
            "nome" => "Conflito Teste",
            "relato" => "Relato do conflito",
            "numeroSeiIdentificacaoConflito" => "",
            "dataInicioConflito" => "2025-10-07",
            "dataAcionamentoMpiConflito" => "2025-10-23",
            "observacoes" => "",
            "regiao" => "Centro-Oeste",
            "uf" => "GO",
            "municipio" => "Águas Lindas de Goiás",
            "terras_indigenas" => [5],
            "povos" => [5],
            "aldeias" => [4],
            "tipos_conflito" => [1],
            "categorias_atores" => [5],
            "assuntos" => [4],
            "atoresIdentificados" => [1],
            "flagHasImpactoAmbiental" => "SIM",
            "flagHasImpactoSaude" => "SIM",
            "flagHasImpactoSocioEconomico" => "SIM",
            "flagHasViolenciaIndigena" => "SIM",
            "flagHasMembroProgramaProtecao" => "NÃO",
            "flagHasBOouNF" => "NÃO",
            "flagHasInquerito" => "NÃO",
            "flagHasProcessoJudicial" => "NÃO",
            "flagHasAssistenciaJuridica" => "NÃO",
            "flagHasRegiaoPrioritaria" => "NÃO",
            "impactos_ambientais" => [1, 2],
            "impactos_saude" => [1, 3],
            "impactos_socio_economicos" => [5, 4],
            "flagHasViolenciaPatrimonialIndigena" => "SIM",
            "flagHasEventoViolenciaIndigena" => "SIM",
            "flagHasAssassinatoPrisaoNaoIndigena" => "SIM",
            "dataViolenciaPessoaNaoIndigena" => "2025-10-02",
            "nomePessoaNaoIndigenaViolentada" => "Tim Lopes",
            "numeroSeiPessoaNaoIndigenaViolentada" => "50303.225456/2022-36",
            "tipoViolenciaPessoaNaoIndigena" => "Assassinato de não indígena",
            "tipoPessoaNaoIndigena" => "Jornalista",
            "violenciasPessoaNaoIndigena" => [
                [
                    "id" => 1759415942451,
                    "tipoViolencia" => "Assassinato de não indígena",
                    "tipoPessoa" => "Povos e comunidades tradicionais (exceto quilombolas e indígenas)",
                    "data" => "2025-09-29",
                    "nome" => "Manuel",
                    "numeroSei" => "50665.656644/6122-22"
                ],
                [
                    "id" => 1759423342612,
                    "tipoViolencia" => "Assassinato de não indígena",
                    "tipoPessoa" => "Jornalista",
                    "data" => "2025-10-02",
                    "nome" => "Tim Lopes",
                    "numeroSei" => "50303.225456/2022-36"
                ]
            ],
            "dataViolenciaPatrimonial" => "2025-10-02",
            "numeroSeiViolenciaPatrimonial" => "50306.336643/2023-11",
            "tipoViolenciaPatrimonial" => "Expulsão",
            "dataViolenciaPessoaIndigena" => "2025-10-02",
            "nomePessoaIndigenaViolentada" => "Anauê",
            "idadePessoaIndigenaViolentada" => "30",
            "numeroSeiViolenciaPessoaIndigena" => "50360.032322/2013-32",
            "tipoViolenciaPessoaIndigena" => "Prisão de indígena",
            "faixaEtariaViolenciaPessoaIndigena" => "Adulto (25-59 anos)",
            "generoViolenciaPessoaIndigena" => "Homem",
            "instrumentoViolenciaPessoaIndigena" => "Projétil não-letal (balas de borracha, bomba de efeito moral)"
        ];

        // Fazer requisição POST
        $response = $this->postJson('/api/conflitos', $payload);

        // Verificar resposta
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'nome',
                'latitude',
                'longitude',
                'relato',
                'dataInicioConflito',
                'dataAcionamentoMpiConflito',
                'regiao',
                'uf',
                'municipio',
                'terras_indigenas' => [
                    '*' => [
                        'id',
                        'idTerraIndigena',
                        'nome'
                    ]
                ],
                'povos' => [
                    '*' => [
                        'id',
                        'idPovo',
                        'nome'
                    ]
                ],
                'aldeias' => [
                    '*' => [
                        'id',
                        'idAldeia',
                        'nome'
                    ]
                ],
                'tipos_conflito' => [
                    '*' => [
                        'id',
                        'idTipoConflito',
                        'nome'
                    ]
                ],
                'categorias_atores' => [
                    '*' => [
                        'id',
                        'idCategoriaAtor',
                        'nome'
                    ]
                ],
                'assuntos' => [
                    '*' => [
                        'id',
                        'idAssunto',
                        'nome'
                    ]
                ],
                'atoresIdentificados' => [
                    '*' => [
                        'id',
                        'idAtor',
                        'nome'
                    ]
                ],
                'impactos_ambientais' => [
                    '*' => [
                        'id',
                        'idImpactoAmbiental',
                        'nome'
                    ]
                ],
                'impactos_saude' => [
                    '*' => [
                        'id',
                        'idImpactoSaude',
                        'nome'
                    ]
                ],
                'impactos_socio_economicos' => [
                    '*' => [
                        'id',
                        'idImpactoSocioEconomico',
                        'nome'
                    ]
                ],
                'flagHasImpactoAmbiental',
                'flagHasImpactoSaude',
                'flagHasImpactoSocioEconomico',
                'flagHasViolenciaIndigena',
                'created_at',
                'updated_at'
            ])
            ->assertJson([
                'nome' => 'Conflito Teste',
                'latitude' => '-16.762467717941604',
                'longitude' => '-54.3218994140625',
                'relato' => 'Relato do conflito',
                'regiao' => 'Centro-Oeste',
                'uf' => 'GO',
                'municipio' => 'Águas Lindas de Goiás',
                'flagHasImpactoAmbiental' => 'SIM',
                'flagHasImpactoSaude' => 'SIM',
                'flagHasImpactoSocioEconomico' => 'SIM',
                'flagHasViolenciaIndigena' => 'SIM'
            ]);

        // Verificar se o conflito foi criado no banco de dados
        $this->assertDatabaseHas('conflito', [
            'nome' => 'Conflito Teste',
            'uf' => 'GO',
            'municipio' => 'Águas Lindas de Goiás'
        ]);

        // Verificar relacionamentos
        $conflitoId = $response->json('id');
        
        $this->assertDatabaseHas('conflito_terra_indigena', [
            'conflito_id' => $conflitoId,
            'terra_indigena_id' => 1 // ID da terra indígena criada
        ]);

        $this->assertDatabaseHas('conflito_povo', [
            'conflito_id' => $conflitoId,
            'povo_id' => 1 // ID do povo criado
        ]);

        $this->assertDatabaseHas('conflito_impacto_ambiental', [
            'conflito_id' => $conflitoId,
            'impacto_ambiental_id' => 1
        ]);

        $this->assertDatabaseHas('conflito_impacto_ambiental', [
            'conflito_id' => $conflitoId,
            'impacto_ambiental_id' => 2
        ]);
    }

    public function test_store_conflito_com_dados_minimos()
    {
        $payload = [
            "latitude" => "-16.762467717941604",
            "longitude" => "-54.3218994140625",
            "nome" => "Conflito Mínimo",
            "relato" => "Relato mínimo do conflito",
            "dataInicioConflito" => "2025-10-07",
            "dataAcionamentoMpiConflito" => "2025-10-23",
            "regiao" => "Centro-Oeste",
            "uf" => "GO",
            "municipio" => "Águas Lindas de Goiás",
            "flagHasImpactoAmbiental" => "NÃO",
            "flagHasImpactoSaude" => "NÃO",
            "flagHasImpactoSocioEconomico" => "NÃO",
            "flagHasViolenciaIndigena" => "NÃO",
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

        $response->assertStatus(201)
            ->assertJson([
                'nome' => 'Conflito Mínimo',
                'uf' => 'GO',
                'flagHasImpactoAmbiental' => 'NÃO'
            ]);

        $this->assertDatabaseHas('conflito', [
            'nome' => 'Conflito Mínimo',
            'uf' => 'GO'
        ]);
    }

    public function test_store_conflito_com_dados_invalidos()
    {
        $payload = [
            "latitude" => "invalid",
            "longitude" => "invalid",
            "nome" => "",
            "relato" => "",
            "dataInicioConflito" => "invalid-date",
            "dataAcionamentoMpiConflito" => "invalid-date",
            "regiao" => "",
            "uf" => "INVALID",
            "municipio" => "",
            "flagHasImpactoAmbiental" => "INVALID"
        ];

        $response = $this->postJson('/api/conflitos', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'latitude',
                'longitude',
                'nome',
                'relato',
                'dataInicioConflito',
                'dataAcionamentoMpiConflito',
                'regiao',
                'uf',
                'municipio',
                'flagHasImpactoAmbiental'
            ]);
    }

    public function test_store_conflito_com_relacionamentos_inexistentes()
    {
        $payload = [
            "latitude" => "-16.762467717941604",
            "longitude" => "-54.3218994140625",
            "nome" => "Conflito com relacionamentos inexistentes",
            "relato" => "Relato do conflito",
            "dataInicioConflito" => "2025-10-07",
            "dataAcionamentoMpiConflito" => "2025-10-23",
            "regiao" => "Centro-Oeste",
            "uf" => "GO",
            "municipio" => "Águas Lindas de Goiás",
            "terras_indigenas" => [999], // ID inexistente
            "povos" => [999], // ID inexistente
            "flagHasImpactoAmbiental" => "NÃO",
            "flagHasImpactoSaude" => "NÃO",
            "flagHasImpactoSocioEconomico" => "NÃO",
            "flagHasViolenciaIndigena" => "NÃO",
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

        // Deve criar o conflito mesmo com relacionamentos inexistentes (ignora os que não existem)
        $response->assertStatus(201);

        $this->assertDatabaseHas('conflito', [
            'nome' => 'Conflito com relacionamentos inexistentes'
        ]);
    }

    private function criarEntidadesRelacionadas()
    {
        // Criar terra indígena
        \App\Models\TerraIndigena::create([
            'idTerraIndigena' => 5,
            'idPovo' => 243,
            'idSituacaoFundiaria' => 5,
            'codigo_ti' => 73793,
            'nome' => 'Aldeia Katurama',
            'superficie_perimetro_ha' => '346.124',
            'modalidade_ti' => 'Dominial Indígena',
            'etnia_nome' => 'Pataxó, Pataxo Há-Há-Há',
            'municipio_nome' => 'São Joaquim de Bicas',
            'uf_sigla' => 'MG',
            'coordenacao_regional' => 'COORDENACAO REGIONAL DE MINAS GERAIS E ESPIRITO SANTO',
            'faixa_fronteira' => 0,
            'undadm_codigo' => '30202002067',
            'undadm_nome' => 'COORDENACAO REGIONAL DE MINAS GERAIS E ESPIRITO SANTO',
            'undadm_sigla' => 'CR-MGES',
            'data_atualizacao' => '2023-04-14',
        ]);

        // Criar povo
        \App\Models\Povo::create([
            'idPovo' => 5,
            'nome' => 'Ajuru',
            'codEtnia' => '005.00',
        ]);

        // Criar aldeia
        \App\Models\Aldeia::create([
            'idAldeia' => 4,
            'cd_uf' => '12',
            'nm_uf' => 'Acre',
            'cd_munic' => '1200500',
            'nm_munic' => 'Sena Madureira',
            'id_li' => '1200425',
            'cd_li' => '1210138',
            'ocorrencia' => '1',
            'nome' => 'Aldeia Indígena Igarapé Preto',
            'cd_setor' => '120050005000123',
            'situacao' => 'Rural',
            'cd_sit' => '8',
            'cd_tipo' => '5',
            'cd_aglom' => '120050000017',
            'nm_aglom' => 'Aldeia Indígena Igarapé Preto',
            'amz_leg' => '1',
            'lat' => '-9.18667996',
            'long' => '-69.22321198',
        ]);

        // Criar tipo de conflito
        \App\Models\TipoConflito::create([
            'idTipoConflito' => 1,
            'nome' => 'Disputas territoriais',
        ]);

        // Criar categoria de ator
        \App\Models\CategoriaAtor::create([
            'idCategoriaAtor' => 5,
            'nome' => 'Caçador',
        ]);

        // Criar assunto
        \App\Models\Assunto::create([
            'idAssunto' => 4,
            'nome' => 'DESMATAMENTO',
        ]);

        // Criar ator
        \App\Models\Ator::create([
            'idAtor' => 1,
            'nome' => 'João da Silva',
        ]);

        // Criar impactos ambientais
        \App\Models\ImpactoAmbiental::create([
            'idImpactoAmbiental' => 1,
            'nome' => 'Contaminação de cursos hídricos',
        ]);

        \App\Models\ImpactoAmbiental::create([
            'idImpactoAmbiental' => 2,
            'nome' => 'Desvio de curso d\'água',
        ]);

        // Criar impactos saúde
        \App\Models\ImpactoSaude::create([
            'idImpactoSaude' => 1,
            'nome' => 'Lesões por acidentes',
        ]);

        \App\Models\ImpactoSaude::create([
            'idImpactoSaude' => 3,
            'nome' => 'Doenças relacionadas ao meio ambiente contaminado, incluindo a falta de saneamenento básico (diarrei',
        ]);

        // Criar impactos socio econômicos
        \App\Models\ImpactoSocioEconomico::create([
            'idImpactoSocioEconomico' => 4,
            'nome' => 'Aumento da violência contra criança e adolescente',
        ]);

        \App\Models\ImpactoSocioEconomico::create([
            'idImpactoSocioEconomico' => 5,
            'nome' => 'Aumento da violência contra pessoas LGBTQIA+',
        ]);
    }
}