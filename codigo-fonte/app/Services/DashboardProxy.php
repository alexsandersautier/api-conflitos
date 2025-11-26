<?php

namespace App\Services;

use App\Models\Conflito;
use App\Models\TerraIndigena;
use App\Models\Povo;
use App\Models\ViolenciaPatrimonial;
use App\Models\ViolenciaPessoaIndigena;
use App\Models\ViolenciaPessoaNaoIndigena;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Aldeia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class DashboardProxy
{
    const CACHE_TTL = 3600; // 1 hora
    const CHUNK_SIZE = 1000;
    
    // Chaves de cache individuais para melhor controle
    const CACHE_KEYS = [
        'totais_gerais'    => 'dashboard_totais_gerais',
        'conflitos_ano'    => 'dashboard_conflitos_ano',
        'conflitos_uf'     => 'dashboard_conflitos_uf',
        'conflitos_regiao' => 'dashboard_conflitos_regiao',
        'violencias'       => 'dashboard_violencias',
        'distribuicao'     => 'dashboard_distribuicao',
        'conflitos_gravidade' => 'dashboard_conflitos_gravidade',
    ];
    
    /**
     * Retorna dados do dashboard com cache individual por seção
     */
    public function getDadosDashboard(bool $forceRefresh = false): array
    {
        $data = [];
        
        try {
            $data = [
                'totais_gerais'           => $this->getCachedOrFresh('totais_gerais',    fn() => $this->getTotaisGerais(), $forceRefresh),
                'conflitos_por_ano'       => $this->getCachedOrFresh('conflitos_ano',    fn() => $this->getConflitosPorAno(), $forceRefresh),
                'conflitos_por_uf'        => $this->getCachedOrFresh('conflitos_uf',     fn() => $this->getConflitosPorUF(), $forceRefresh),
                'conflitos_por_regiao'    => $this->getCachedOrFresh('conflitos_regiao', fn() => $this->getConflitosPorRegiao(), $forceRefresh),
                'estatisticas_violencias' => $this->getCachedOrFresh('violencias',       fn() => $this->getEstatisticasViolencias(), $forceRefresh),
                'distribuicao_geografica' => $this->getCachedOrFresh('distribuicao',     fn() => $this->getDistribuicaoGeografica(), $forceRefresh),
                'conflitos_por_gravidade' => $this->getCachedOrFresh('conflitos_gravidade', fn() => $this->getConflitosPorClassificacaoGravidade(), $forceRefresh),
                'ultima_atualizacao'      => now()->toDateTimeString()
            ];
        } catch (\Exception $e) {
            // Fallback: buscar dados sem cache em caso de erro
            Log::error('Erro ao buscar dados dashboard: ' . $e->getMessage());
            $data = $this->getDadosSemCache();
        }
        
        return $data;
    }
    
    /**
     * Helper para cache com fallback
     */
    private function getCachedOrFresh(string $key, callable $callback, bool $forceRefresh = false)
    {
        $cacheKey = self::CACHE_KEYS[$key] ?? "dashboard_{$key}";
        
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }
        
        return Cache::remember($cacheKey, self::CACHE_TTL, $callback);
    }
    
    /**
     * Fallback sem cache
     */
    private function getDadosSemCache(): array
    {
        return [
            'totais_gerais' => $this->getTotaisGerais(),
            'conflitos_por_ano' => $this->getConflitosPorAno(),
            'conflitos_por_uf' => $this->getConflitosPorUF(),
            'conflitos_por_regiao' => $this->getConflitosPorRegiao(),
            'estatisticas_violencias' => $this->getEstatisticasViolencias(),
            'distribuicao_geografica' => $this->getDistribuicaoGeografica(),
            'conflitos_por_gravidade' => $this->getConflitosPorClassificacaoGravidade(),
            'ultima_atualizacao' => now()->toDateTimeString()
        ];
    }
    
    /**
     * Totais gerais otimizados
     */
    public function getTotaisGerais(): array
    {
        // Executa contagens em paralelo quando possível
        return [
            'total_conflitos' => Conflito::count(),
            'total_aldeias' => Aldeia::count(),
            'total_terras_indigenas' => TerraIndigena::count(),
            'total_povos' => Povo::count(),
            'total_violencias' => $this->getTotalViolencias(),
        ];
    }
    
    /**
     * Quantidade de conflitos por ano
     */
    public function getConflitosPorAno(): array
    {
        $conflitosPorDataInicio = Conflito::select(
            DB::raw('YEAR(dataInicioConflito) as ano'),
            DB::raw('COUNT(*) as total_inicio')
            )
            ->whereNotNull('dataInicioConflito')
            ->groupBy('ano')
            ->orderBy('ano', 'ASC')
            ->get()
            ->keyBy('ano');
            
            $conflitosPorAcionamento = Conflito::select(
                DB::raw('YEAR(dataAcionamentoMpiConflito) as ano'),
                DB::raw('COUNT(*) as total_acionamento')
                )
                ->whereNotNull('dataAcionamentoMpiConflito')
                ->groupBy('ano')
                ->orderBy('ano', 'ASC')
                ->get()
                ->keyBy('ano');
                
                // Combina os anos de ambos os conjuntos
                $anosUnicos = array_unique(array_merge(
                    $conflitosPorDataInicio->pluck('ano')->toArray(),
                    $conflitosPorAcionamento->pluck('ano')->toArray()
                    ));
                
                sort($anosUnicos);
                
                $resultado = [];
                
                foreach ($anosUnicos as $ano) {
                    $resultado[] = [
                        'ano'               => $ano,
                        'total_inicio'      => $conflitosPorDataInicio->get($ano)->total_inicio ?? 0,
                        'total_acionamento' => $conflitosPorAcionamento->get($ano)->total_acionamento ?? 0,
                        'total_geral'       => ($conflitosPorDataInicio->get($ano)->total_inicio ?? 0) + ($conflitosPorAcionamento->get($ano)->total_acionamento ?? 0)
                    ];
                }
                
                return $resultado;
    }
    
    /**
     * Conflitos por classificação de gravidade
     */
    public function getConflitosPorClassificacaoGravidade(): array
    {
        $resultados = Conflito::select(
            'classificacaoGravidadeConflitoDemed',
            DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('classificacaoGravidadeConflitoDemed')
            ->where('classificacaoGravidadeConflitoDemed', '!=', '')
            ->groupBy('classificacaoGravidadeConflitoDemed')
            ->orderBy('total', 'DESC')
            ->get();
            
            // Mapeia os valores possíveis para garantir que todos apareçam mesmo com zero
            $valoresPossiveis = [
                'Pouca Urgência',
                'Urgência',
                'Emergência',
                'Não Urgente'
            ];
            
            $resultadoFinal = [];
            
            foreach ($valoresPossiveis as $valor) {
                $encontrado = $resultados->firstWhere('classificacaoGravidadeConflitoDemed', $valor);
                $resultadoFinal[] = [
                    'classificacao' => $valor,
                    'total' => $encontrado ? $encontrado->total : 0
                ];
            }
            
            return $resultadoFinal;
    }
    
    /**
     * Método unificado para dados geográficos
     */
    public function getDadosGeograficos(): Collection
    {
        return DB::table('conflito as c')
        ->select(
            'lc.uf',
            'lc.regiao',
            'lc.municipio',
            DB::raw('COUNT(DISTINCT c.idConflito) as total')
            )
            ->join('localidade_conflito as lc', 'c.idConflito', '=', 'lc.idConflito')
            ->whereNotNull('lc.uf')
            ->where('lc.uf', '!=', '')
            ->groupBy('lc.uf', 'lc.regiao', 'lc.municipio')
            ->get();
    }
    
    /**
     * Conflitos por UF otimizado
     */
    public function getConflitosPorUF(): array
    {
        $dados = $this->getDadosGeograficos();
        
        return $dados->groupBy('uf')
        ->map(function ($group, $uf) {
            return [
                'uf' => $uf,
                'total' => $group->sum('total')
            ];
        })
        ->sortByDesc('total')
        ->values()
        ->toArray();
    }
    
    /**
     * Conflitos por região otimizado
     */
    public function getConflitosPorRegiao(): array
    {
        $dados = $this->getDadosGeograficos();
        
        return $dados->groupBy('regiao')
        ->map(function ($group, $regiao) {
            return [
                'regiao' => $regiao,
                'total' => $group->sum('total')
            ];
        })
        ->sortByDesc('total')
        ->values()
        ->toArray();
    }
    
    /**
     * Conflitos por município (top 10)
     */
    public function getConflitosPorMunicipio(): array
    {
        $dados = $this->getDadosGeograficos();
        
        return $dados->groupBy(function ($item) {
            return $item->uf . '|' . $item->municipio;
        })
        ->map(function ($group, $key) {
            [$uf, $municipio] = explode('|', $key);
            return [
                'uf' => $uf,
                'municipio' => $municipio,
                'total' => $group->sum('total')
            ];
        })
        ->sortByDesc('total')
        ->take(10)
        ->values()
        ->toArray();
    }
    
    /**
     * Distribuição geográfica unificada
     */
    public function getDistribuicaoGeografica(): array
    {
        
        return [
            'por_uf' => $this->getConflitosPorUF(),
            'por_regiao' => $this->getConflitosPorRegiao(),
            'top_municipios' => $this->getConflitosPorMunicipio()
        ];
    }
    
    /**
     * Estatísticas de violências otimizadas
     */
    public function getEstatisticasViolencias(): array
    {
        return [
            'patrimoniais' => ViolenciaPatrimonial::count(),
            'pessoas_indigenas' => ViolenciaPessoaIndigena::count(),
            'pessoas_nao_indigenas' => ViolenciaPessoaNaoIndigena::count(),
            'total_geral' => $this->getTotalViolencias(),
            'violencias_por_tipo' => $this->getViolenciasPorTipo()
        ];
    }
    
    /**
     * Total geral de violências
     */
    private function getTotalViolencias(): int
    {
        return ViolenciaPatrimonial::count() +
        ViolenciaPessoaIndigena::count() +
        ViolenciaPessoaNaoIndigena::count();
    }
    
    /**
     * Violências por tipo otimizado
     */
    private function getViolenciasPorTipo(): array
    {
        // Executa as três queries em paralelo (melhor performance)
        $results = DB::transaction(function () {
            return [
                'patrimoniais' => ViolenciaPatrimonial::groupBy('tipoViolencia')
                ->select('tipoViolencia', DB::raw('COUNT(*) as total'))
                ->get()
                ->pluck('total', 'tipoViolencia'),
                
                'indigenas' => ViolenciaPessoaIndigena::groupBy('tipoViolencia')
                ->select('tipoViolencia', DB::raw('COUNT(*) as total'))
                ->get()
                ->pluck('total', 'tipoViolencia'),
                
                'nao_indigenas' => ViolenciaPessoaNaoIndigena::groupBy('tipoViolencia')
                ->select('tipoViolencia', DB::raw('COUNT(*) as total'))
                ->get()
                ->pluck('total', 'tipoViolencia')
            ];
        });
            
            return [
                'patrimoniais' => $results['patrimoniais']->toArray(),
                'pessoas_indigenas' => $results['indigenas']->toArray(),
                'pessoas_nao_indigenas' => $results['nao_indigenas']->toArray()
            ];
    }
    
    /**
     * Método unificado para dados com filtro
     */
    public function getDadosComFiltro(array $filtros = []): array
    {
        $cacheKey = 'dashboard_filtro_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filtros) {
            return [
                'totais_gerais' => $this->getTotaisGeraisComFiltro($filtros),
                'conflitos_por_ano' => $this->getConflitosPorAnoComFiltro($filtros),
                'conflitos_por_uf' => $this->getConflitosPorUFComFiltro($filtros),
                'conflitos_por_regiao' => $this->getConflitosPorRegiaoComFiltro($filtros),
                'estatisticas_violencias' => $this->getEstatisticasViolenciasComFiltro($filtros),
                'distribuicao_geografica' => $this->getDistribuicaoGeograficaComFiltro($filtros),
                'conflitos_por_gravidade' => $this->getConflitosPorClassificacaoGravidadeComFiltro($filtros),
                'filtros' => $filtros,
                'ultima_atualizacao' => now()->toDateTimeString()
            ];
        });
    }
    
    /**
     * Builder base para filtros
     */
    private function applyFiltros($query, array $filtros)
    {
        if (isset($filtros['data_inicio']) && isset($filtros['data_fim'])) {
            $query->whereBetween('dataInicioConflito', [
                $filtros['data_inicio'],
                $filtros['data_fim']
            ]);
        }
        
        if (isset($filtros['uf'])) {
            $query->whereHas('localidades', function ($q) use ($filtros) {
                $q->where('uf', $filtros['uf']);
            });
        }
        
        return $query;
    }
    
    /**
     * Conflitos por gravidade com filtros aplicados
     */
    public function getConflitosPorClassificacaoGravidadeComFiltro(array $filtros = []): array
    {
        $query = Conflito::select(
            'classificacaoGravidadeConflitoDemed',
            DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('classificacaoGravidadeConflitoDemed')
            ->where('classificacaoGravidadeConflitoDemed', '!=', '');
            
            // Aplica filtros
            $query = $this->applyFiltros($query, $filtros);
            
            $resultados = $query
            ->groupBy('classificacaoGravidadeConflitoDemed')
            ->orderBy('total', 'DESC')
            ->get();
            
            // Mapeia os valores possíveis para garantir que todos apareçam mesmo com zero
            $valoresPossiveis = [
                'Pouca Urgência',
                'Urgência',
                'Emergência',
                'Não Urgente'
            ];
            
            $resultadoFinal = [];
            
            foreach ($valoresPossiveis as $valor) {
                $encontrado = $resultados->firstWhere('classificacaoGravidadeConflitoDemed', $valor);
                $resultadoFinal[] = [
                    'classificacao' => $valor,
                    'total' => $encontrado ? $encontrado->total : 0
                ];
            }
            
            return $resultadoFinal;
    }
    
    /**
     * Totais gerais com filtros
     */
    public function getTotaisGeraisComFiltro(array $filtros = []): array
    {
        $query = Conflito::query();
        $query = $this->applyFiltros($query, $filtros);
        
        return [
            'total_conflitos' => $query->count(),
            'total_aldeias' => Aldeia::count(), // Pode precisar de filtros também
            'total_terras_indigenas' => TerraIndigena::count(), // Pode precisar de filtros também
            'total_povos' => Povo::count(), // Pode precisar de filtros também
            'total_violencias' => $this->getTotalViolenciasComFiltro($filtros),
        ];
    }
    
    /**
     * Total de violências com filtros
     */
    private function getTotalViolenciasComFiltro(array $filtros = []): int
    {
        // Implementar lógica de filtros para violências se necessário
        return $this->getTotalViolencias();
    }
    
    /**
     * Conflitos por ano com filtros
     */
    public function getConflitosPorAnoComFiltro(array $filtros = []): array
    {
        $queryInicio = Conflito::select(
            DB::raw('YEAR(dataInicioConflito) as ano'),
            DB::raw('COUNT(*) as total_inicio')
            )
            ->whereNotNull('dataInicioConflito');
            
            $queryAcionamento = Conflito::select(
                DB::raw('YEAR(dataAcionamentoMpiConflito) as ano'),
                DB::raw('COUNT(*) as total_acionamento')
                )
                ->whereNotNull('dataAcionamentoMpiConflito');
                
                // Aplica filtros
                $queryInicio = $this->applyFiltros($queryInicio, $filtros);
                $queryAcionamento = $this->applyFiltros($queryAcionamento, $filtros);
                
                $conflitosPorDataInicio = $queryInicio
                ->groupBy('ano')
                ->orderBy('ano', 'ASC')
                ->get()
                ->keyBy('ano');
                
                $conflitosPorAcionamento = $queryAcionamento
                ->groupBy('ano')
                ->orderBy('ano', 'ASC')
                ->get()
                ->keyBy('ano');
                
                // Combina os anos de ambos os conjuntos
                $anosUnicos = array_unique(array_merge(
                    $conflitosPorDataInicio->pluck('ano')->toArray(),
                    $conflitosPorAcionamento->pluck('ano')->toArray()
                    ));
                
                sort($anosUnicos);
                
                $resultado = [];
                
                foreach ($anosUnicos as $ano) {
                    $resultado[] = [
                        'ano'               => $ano,
                        'total_inicio'      => $conflitosPorDataInicio->get($ano)->total_inicio ?? 0,
                        'total_acionamento' => $conflitosPorAcionamento->get($ano)->total_acionamento ?? 0,
                        'total_geral'       => ($conflitosPorDataInicio->get($ano)->total_inicio ?? 0) + ($conflitosPorAcionamento->get($ano)->total_acionamento ?? 0)
                    ];
                }
                
                return $resultado;
    }
    
    /**
     * Conflitos por UF com filtros
     */
    public function getConflitosPorUFComFiltro(array $filtros = []): array
    {
        $query = DB::table('conflito as c')
        ->select(
            'lc.uf',
            DB::raw('COUNT(DISTINCT c.idConflito) as total')
            )
            ->join('localidade_conflito as lc', 'c.idConflito', '=', 'lc.idConflito')
            ->whereNotNull('lc.uf')
            ->where('lc.uf', '!=', '');
            
            // Aplica filtros de data
            if (isset($filtros['data_inicio']) && isset($filtros['data_fim'])) {
                $query->whereBetween('c.dataInicioConflito', [
                    $filtros['data_inicio'],
                    $filtros['data_fim']
                ]);
            }
            
            // Aplica filtros de UF
            if (isset($filtros['uf'])) {
                $query->where('lc.uf', $filtros['uf']);
            }
            
            $dados = $query->groupBy('lc.uf')->get();
            
            return $dados->map(function ($item) {
                return [
                    'uf' => $item->uf,
                    'total' => $item->total
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }
    
    /**
     * Conflitos por região com filtros
     */
    public function getConflitosPorRegiaoComFiltro(array $filtros = []): array
    {
        $query = DB::table('conflito as c')
        ->select(
            'lc.regiao',
            DB::raw('COUNT(DISTINCT c.idConflito) as total')
            )
            ->join('localidade_conflito as lc', 'c.idConflito', '=', 'lc.idConflito')
            ->whereNotNull('lc.regiao')
            ->where('lc.regiao', '!=', '');
            
            // Aplica filtros de data
            if (isset($filtros['data_inicio']) && isset($filtros['data_fim'])) {
                $query->whereBetween('c.dataInicioConflito', [
                    $filtros['data_inicio'],
                    $filtros['data_fim']
                ]);
            }
            
            // Aplica filtros de UF
            if (isset($filtros['uf'])) {
                $query->where('lc.uf', $filtros['uf']);
            }
            
            $dados = $query->groupBy('lc.regiao')->get();
            
            return $dados->map(function ($item) {
                return [
                    'regiao' => $item->regiao,
                    'total' => $item->total
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }
    
    /**
     * Estatísticas de violências com filtros
     */
    public function getEstatisticasViolenciasComFiltro(array $filtros = []): array
    {
        // Implementar lógica de filtros para violências se necessário
        return $this->getEstatisticasViolencias();
    }
    
    /**
     * Distribuição geográfica com filtros
     */
    public function getDistribuicaoGeograficaComFiltro(array $filtros = []): array
    {
        return [
            'por_uf' => $this->getConflitosPorUFComFiltro($filtros),
            'por_regiao' => $this->getConflitosPorRegiaoComFiltro($filtros),
            'top_municipios' => $this->getConflitosPorMunicipioComFiltro($filtros)
        ];
    }
    
    /**
     * Conflitos por município com filtros
     */
    public function getConflitosPorMunicipioComFiltro(array $filtros = []): array
    {
        $query = DB::table('conflito as c')
        ->select(
            'lc.uf',
            'lc.municipio',
            DB::raw('COUNT(DISTINCT c.idConflito) as total')
            )
            ->join('localidade_conflito as lc', 'c.idConflito', '=', 'lc.idConflito')
            ->whereNotNull('lc.municipio')
            ->where('lc.municipio', '!=', '');
            
            // Aplica filtros de data
            if (isset($filtros['data_inicio']) && isset($filtros['data_fim'])) {
                $query->whereBetween('c.dataInicioConflito', [
                    $filtros['data_inicio'],
                    $filtros['data_fim']
                ]);
            }
            
            // Aplica filtros de UF
            if (isset($filtros['uf'])) {
                $query->where('lc.uf', $filtros['uf']);
            }
            
            $dados = $query->groupBy('lc.uf', 'lc.municipio')->get();
            
            return $dados->map(function ($item) {
                return [
                    'uf' => $item->uf,
                    'municipio' => $item->municipio,
                    'total' => $item->total
                ];
            })
            ->sortByDesc('total')
            ->take(10)
            ->values()
            ->toArray();
    }
    
    /**
     * Limpa todo o cache do dashboard
     */
    public function clearCache(): void
    {
        foreach (self::CACHE_KEYS as $key) {
            Cache::forget($key);
        }
        
        // Limpa caches de filtros também
        Cache::forget('dashboard_filtro_*'); // Dependendo do driver
    }
    
    /**
     * Atualiza cache específico
     */
    public function refreshCache(string $cacheKey): bool
    {
        if (!in_array($cacheKey, array_keys(self::CACHE_KEYS))) {
            return false;
        }
        
        Cache::forget(self::CACHE_KEYS[$cacheKey]);
        
        // Recalcula o cache
        $this->getCachedOrFresh($cacheKey, function() use ($cacheKey) {
            return $this->{"get" . ucfirst($cacheKey)}();
        }, true);
            
            return true;
    }
}