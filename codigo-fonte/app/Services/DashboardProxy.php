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

class DashboardProxy
{
    /**
     * Retorna todos os dados do dashboard em uma única chamada
     */
    public function getDadosDashboard(): array
    {
        return Cache::remember('dashboard_data', 300, function () {
            return [
                'totais_gerais' => $this->getTotaisGerais(),
                'conflitos_por_ano' => $this->getConflitosPorAno(),
                'conflitos_por_uf' => $this->getConflitosPorUF(),
                'conflitos_por_regiao' => $this->getConflitosPorRegiao(),
                'estatisticas_violencias' => $this->getEstatisticasViolencias(),
                'distribuicao_geografica' => $this->getDistribuicaoGeografica(),
                'ultima_atualizacao' => now()->toDateTimeString()
            ];
        });
    }

    /**
     * Totais gerais do sistema
     */
    public function getTotaisGerais(): array
    {
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
        return Conflito::select(
                DB::raw('YEAR(dataInicioConflito) as ano'),
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('dataInicioConflito')
            ->groupBy('ano')
            ->orderBy('ano', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'ano' => $item->ano,
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    /**
     * Total de conflitos por UF - usando localidade_conflito
     */
    public function getConflitosPorUF(): array
    {
        return DB::table('conflito as c')
            ->select(
                'lc.uf',
                DB::raw('COUNT(DISTINCT c.idConflito) as total')
            )
            ->join('localidade_conflito as lc', 'c.idConflito', '=', 'lc.idConflito')
            ->whereNotNull('lc.uf')
            ->where('lc.uf', '!=', '')
            ->groupBy('lc.uf')
            ->orderBy('total', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'uf' => $item->uf,
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    /**
     * Conflitos por região (usando localidade_conflito)
     */
    public function getConflitosPorRegiao(): array
    {
        return DB::table('conflito as c')
            ->select(
                'lc.regiao',
                DB::raw('COUNT(DISTINCT c.idConflito) as total')
            )
            ->join('localidade_conflito as lc', 'c.idConflito', '=', 'lc.idConflito')
            ->whereNotNull('lc.regiao')
            ->where('lc.regiao', '!=', '')
            ->groupBy('lc.regiao')
            ->orderBy('total', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'regiao' => $item->regiao,
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    /**
     * Conflitos por município (top 10)
     */
    public function getConflitosPorMunicipio(): array
    {
        return DB::table('conflito as c')
            ->select(
                'lc.uf',
                'lc.municipio',
                DB::raw('COUNT(DISTINCT c.idConflito) as total')
            )
            ->join('localidade_conflito as lc', 'c.idConflito', '=', 'lc.idConflito')
            ->whereNotNull('lc.municipio')
            ->where('lc.municipio', '!=', '')
            ->groupBy('lc.uf', 'lc.municipio')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'uf' => $item->uf,
                    'municipio' => $item->municipio,
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    /**
     * Distribuição geográfica completa
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
     * Estatísticas detalhadas de violências
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
     * Agrupa violências por tipo
     */
    private function getViolenciasPorTipo(): array
    {
        $patrimoniais = ViolenciaPatrimonial::select('tipoViolencia', DB::raw('COUNT(*) as total'))
            ->groupBy('tipoViolencia')
            ->get()
            ->pluck('total', 'tipoViolencia')
            ->toArray();

        $indigenas = ViolenciaPessoaIndigena::select('tipoViolencia', DB::raw('COUNT(*) as total'))
            ->groupBy('tipoViolencia')
            ->get()
            ->pluck('total', 'tipoViolencia')
            ->toArray();

        $naoIndigenas = ViolenciaPessoaNaoIndigena::select('tipoViolencia', DB::raw('COUNT(*) as total'))
            ->groupBy('tipoViolencia')
            ->get()
            ->pluck('total', 'tipoViolencia')
            ->toArray();

        return [
            'patrimoniais' => $patrimoniais,
            'pessoas_indigenas' => $indigenas,
            'pessoas_nao_indigenas' => $naoIndigenas
        ];
    }

    /**
     * Método para forçar atualização do cache
     */
    public function clearCache(): bool
    {
        return Cache::forget('dashboard_data');
    }

    /**
     * Retorna dados do dashboard com filtro por período
     */
    public function getDadosDashboardComFiltro(string $dataInicio, string $dataFim): array
    {
        $cacheKey = "dashboard_data_{$dataInicio}_{$dataFim}";
        
        return Cache::remember($cacheKey, 300, function () use ($dataInicio, $dataFim) {
            return [
                'totais_gerais' => $this->getTotaisGeraisComFiltro($dataInicio, $dataFim),
                'conflitos_por_ano' => $this->getConflitosPorAnoComFiltro($dataInicio, $dataFim),
                'conflitos_por_uf' => $this->getConflitosPorUFComFiltro($dataInicio, $dataFim),
                'conflitos_por_regiao' => $this->getConflitosPorRegiaoComFiltro($dataInicio, $dataFim),
                'estatisticas_violencias' => $this->getEstatisticasViolenciasComFiltro($dataInicio, $dataFim),
                'distribuicao_geografica' => $this->getDistribuicaoGeograficaComFiltro($dataInicio, $dataFim),
                'periodo' => [
                    'data_inicio' => $dataInicio,
                    'data_fim' => $dataFim
                ],
                'ultima_atualizacao' => now()->toDateTimeString()
            ];
        });
    }

    /**
     * Métodos com filtro por período
     */
    private function getTotaisGeraisComFiltro(string $dataInicio, string $dataFim): array
    {
        return [
            'total_conflitos' => Conflito::whereBetween('dataInicioConflito', [$dataInicio, $dataFim])->count(),
            'total_terras_indigenas' => TerraIndigena::count(),
            'total_povos' => Povo::count(),
            'total_violencias' => $this->getTotalViolenciasComFiltro($dataInicio, $dataFim),
        ];
    }

    private function getConflitosPorAnoComFiltro(string $dataInicio, string $dataFim): array
    {
        return Conflito::select(
                DB::raw('YEAR(dataInicioConflito) as ano'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('dataInicioConflito', [$dataInicio, $dataFim])
            ->groupBy('ano')
            ->orderBy('ano', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'ano' => $item->ano,
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    private function getConflitosPorUFComFiltro(string $dataInicio, string $dataFim): array
    {
        return DB::table('conflito as c')
            ->select(
                'lc.uf',
                DB::raw('COUNT(DISTINCT c.idConflito) as total')
            )
            ->join('localidade_conflito as lc', 'c.idConflito', '=', 'lc.idConflito')
            ->whereBetween('c.dataInicioConflito', [$dataInicio, $dataFim])
            ->whereNotNull('lc.uf')
            ->where('lc.uf', '!=', '')
            ->groupBy('lc.uf')
            ->orderBy('total', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'uf' => $item->uf,
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    private function getConflitosPorRegiaoComFiltro(string $dataInicio, string $dataFim): array
    {
        return DB::table('conflito as c')
            ->select(
                'lc.regiao',
                DB::raw('COUNT(DISTINCT c.idConflito) as total')
            )
            ->join('localidade_conflito as lc', 'c.idConflito', '=', 'lc.idConflito')
            ->whereBetween('c.dataInicioConflito', [$dataInicio, $dataFim])
            ->whereNotNull('lc.regiao')
            ->where('lc.regiao', '!=', '')
            ->groupBy('lc.regiao')
            ->orderBy('total', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'regiao' => $item->regiao,
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    private function getDistribuicaoGeograficaComFiltro(string $dataInicio, string $dataFim): array
    {
        return [
            'por_uf' => $this->getConflitosPorUFComFiltro($dataInicio, $dataFim),
            'por_regiao' => $this->getConflitosPorRegiaoComFiltro($dataInicio, $dataFim),
            'top_municipios' => $this->getConflitosPorMunicipioComFiltro($dataInicio, $dataFim)
        ];
    }

    private function getConflitosPorMunicipioComFiltro(string $dataInicio, string $dataFim): array
    {
        return DB::table('conflito as c')
            ->select(
                'lc.uf',
                'lc.municipio',
                DB::raw('COUNT(DISTINCT c.idConflito) as total')
            )
            ->join('localidade_conflito as lc', 'c.idConflito', '=', 'lc.idConflito')
            ->whereBetween('c.dataInicioConflito', [$dataInicio, $dataFim])
            ->whereNotNull('lc.municipio')
            ->where('lc.municipio', '!=', '')
            ->groupBy('lc.uf', 'lc.municipio')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'uf' => $item->uf,
                    'municipio' => $item->municipio,
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    private function getEstatisticasViolenciasComFiltro(string $dataInicio, string $dataFim): array
    {
        return [
            'patrimoniais' => ViolenciaPatrimonial::whereBetween('data', [$dataInicio, $dataFim])->count(),
            'pessoas_indigenas' => ViolenciaPessoaIndigena::whereBetween('data', [$dataInicio, $dataFim])->count(),
            'pessoas_nao_indigenas' => ViolenciaPessoaNaoIndigena::whereBetween('data', [$dataInicio, $dataFim])->count(),
            'total_geral' => $this->getTotalViolenciasComFiltro($dataInicio, $dataFim),
            'violencias_por_tipo' => $this->getViolenciasPorTipoComFiltro($dataInicio, $dataFim)
        ];
    }

    private function getTotalViolenciasComFiltro(string $dataInicio, string $dataFim): int
    {
        return ViolenciaPatrimonial::whereBetween('data', [$dataInicio, $dataFim])->count() + 
               ViolenciaPessoaIndigena::whereBetween('data', [$dataInicio, $dataFim])->count() + 
               ViolenciaPessoaNaoIndigena::whereBetween('data', [$dataInicio, $dataFim])->count();
    }

    private function getViolenciasPorTipoComFiltro(string $dataInicio, string $dataFim): array
    {
        $patrimoniais = ViolenciaPatrimonial::select('tipoViolencia', DB::raw('COUNT(*) as total'))
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->groupBy('tipoViolencia')
            ->get()
            ->pluck('total', 'tipoViolencia')
            ->toArray();

        $indigenas = ViolenciaPessoaIndigena::select('tipoViolencia', DB::raw('COUNT(*) as total'))
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->groupBy('tipoViolencia')
            ->get()
            ->pluck('total', 'tipoViolencia')
            ->toArray();

        $naoIndigenas = ViolenciaPessoaNaoIndigena::select('tipoViolencia', DB::raw('COUNT(*) as total'))
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->groupBy('tipoViolencia')
            ->get()
            ->pluck('total', 'tipoViolencia')
            ->toArray();

        return [
            'patrimoniais' => $patrimoniais,
            'pessoas_indigenas' => $indigenas,
            'pessoas_nao_indigenas' => $naoIndigenas
        ];
    }
}