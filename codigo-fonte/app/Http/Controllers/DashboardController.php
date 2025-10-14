<?php

namespace App\Http\Controllers;

use App\Services\DashboardProxy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardProxy;

    public function __construct(DashboardProxy $dashboardProxy)
    {
        $this->dashboardProxy = $dashboardProxy;
    }

    /**
     * Retorna todos os dados do dashboard
     */
    public function getDadosDashboard(): JsonResponse
    {
        try {
            $dados = $this->dashboardProxy->getDadosDashboard();

            return response()->json($dados);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar dados do dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna dados do dashboard com filtro por período
     */
    public function getDadosDashboardComFiltro(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dados = $this->dashboardProxy->getDadosDashboardComFiltro(
                $request->data_inicio,
                $request->data_fim
            );

            return response()->json($dados);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar dados do dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna apenas os totais gerais do dashboard
     */
    public function getTotaisGerais(): JsonResponse
    {
        try {
            $totais = $this->dashboardProxy->getTotaisGerais();

            return response()->json($totais);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar totais gerais: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna a distribuição geográfica completa
     */
    public function getDistribuicaoGeografica(): JsonResponse
    {
        try {
            $distribuicao = $this->dashboardProxy->getDistribuicaoGeografica();

            return response()->json($distribuicao);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar distribuição geográfica: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna conflitos por UF
     */
    public function getConflitosPorUF(): JsonResponse
    {
        try {
            $conflitosPorUF = $this->dashboardProxy->getConflitosPorUF();

            return response()->json($conflitosPorUF);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar conflitos por UF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna conflitos por região
     */
    public function getConflitosPorRegiao(): JsonResponse
    {
        try {
            $conflitosPorRegiao = $this->dashboardProxy->getConflitosPorRegiao();

            return response()->json($conflitosPorRegiao);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar conflitos por região: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna top municípios com mais conflitos
     */
    public function getConflitosPorMunicipio(): JsonResponse
    {
        try {
            $conflitosPorMunicipio = $this->dashboardProxy->getConflitosPorMunicipio();

            return response()->json($conflitosPorMunicipio);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar conflitos por município: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna conflitos por ano
     */
    public function getConflitosPorAno(): JsonResponse
    {
        try {
            $conflitosPorAno = $this->dashboardProxy->getConflitosPorAno();

            return response()->json($conflitosPorAno);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar conflitos por ano: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna estatísticas de violências
     */
    public function getEstatisticasViolencias(): JsonResponse
    {
        try {
            $estatisticas = $this->dashboardProxy->getEstatisticasViolencias();

            return response()->json($estatisticas);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar estatísticas de violências: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna dados específicos com filtros avançados
     */
    public function getDadosFiltradosAvancados(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'uf' => 'nullable|string|max:2',
            'regiao' => 'nullable|string|max:100',
            'tipo_dado' => 'required|in:totais,geografia,violencias,todos'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dadosFiltrados = $this->processarDadosFiltrados($request);

            return response()->json([
                'success' => true,
                'data' => $dadosFiltrados,
                'filtros_aplicados' => $request->only(['data_inicio', 'data_fim', 'uf', 'regiao', 'tipo_dado']),
                'message' => 'Dados filtrados recuperados com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar dados filtrados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processa os dados com filtros avançados
     */
    private function processarDadosFiltrados(Request $request): array
    {
        $tipoDado = $request->tipo_dado;
        $dataInicio = $request->data_inicio;
        $dataFim = $request->data_fim;

        $dados = [];

        switch ($tipoDado) {
            case 'totais':
                $dados = $dataInicio && $dataFim 
                    ? $this->dashboardProxy->getTotaisGeraisComFiltro($dataInicio, $dataFim)
                    : $this->dashboardProxy->getTotaisGerais();
                break;

            case 'geografia':
                $dados = $dataInicio && $dataFim 
                    ? $this->dashboardProxy->getDistribuicaoGeograficaComFiltro($dataInicio, $dataFim)
                    : $this->dashboardProxy->getDistribuicaoGeografica();
                break;

            case 'violencias':
                $dados = $dataInicio && $dataFim 
                    ? $this->dashboardProxy->getEstatisticasViolenciasComFiltro($dataInicio, $dataFim)
                    : $this->dashboardProxy->getEstatisticasViolencias();
                break;

            case 'todos':
                $dados = $dataInicio && $dataFim 
                    ? $this->dashboardProxy->getDadosDashboardComFiltro($dataInicio, $dataFim)
                    : $this->dashboardProxy->getDadosDashboard();
                break;
        }

        // Aplicar filtros adicionais de UF e região se for geografia
        if ($tipoDado === 'geografia' || $tipoDado === 'todos') {
            $dados = $this->aplicarFiltrosGeografia($dados, $request->uf, $request->regiao);
        }

        return $dados;
    }

    /**
     * Aplica filtros adicionais de UF e região
     */
    private function aplicarFiltrosGeografia(array $dados, ?string $uf, ?string $regiao): array
    {
        if (isset($dados['por_uf']) && $uf) {
            $dados['por_uf'] = array_filter($dados['por_uf'], function ($item) use ($uf) {
                return $item['uf'] === $uf;
            });
        }

        if (isset($dados['por_regiao']) && $regiao) {
            $dados['por_regiao'] = array_filter($dados['por_regiao'], function ($item) use ($regiao) {
                return $item['regiao'] === $regiao;
            });
        }

        if (isset($dados['top_municipios']) && $uf) {
            $dados['top_municipios'] = array_filter($dados['top_municipios'], function ($item) use ($uf) {
                return $item['uf'] === $uf;
            });
        }

        return $dados;
    }

    /**
     * Retorna métricas em tempo real (sem cache)
     */
    public function getMetricasTempoReal(): JsonResponse
    {
        try {
            // Força limpeza do cache para dados em tempo real
            $this->dashboardProxy->clearCache();

            $dados = $this->dashboardProxy->getDadosDashboard();

            return response()->json([
                'success' => true,
                'data' => $dados,
                'cache' => false,
                'message' => 'Métricas em tempo real recuperadas com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar métricas em tempo real: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Força atualização do cache do dashboard
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->dashboardProxy->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Cache do dashboard limpo com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao limpar cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Health check do dashboard
     */
    public function healthCheck(): JsonResponse
    {
        try {
            $health = [
                'status' => 'healthy',
                'timestamp' => now()->toDateTimeString(),
                'services' => [
                    'database' => $this->checkDatabase(),
                    'cache' => $this->checkCache(),
                    'auth' => Auth::check() ? 'authenticated' : 'unauthenticated'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $health,
                'message' => 'Health check realizado com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'unhealthy',
                'message' => 'Erro no health check: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica conexão com o banco
     */
    private function checkDatabase(): string
    {
        try {
            \DB::connection()->getPdo();
            return 'connected';
        } catch (\Exception $e) {
            return 'disconnected';
        }
    }

    /**
     * Verifica status do cache
     */
    private function checkCache(): string
    {
        try {
            \Cache::put('health_check', 'ok', 10);
            return \Cache::get('health_check') === 'ok' ? 'working' : 'failing';
        } catch (\Exception $e) {
            return 'failing';
        }
    }
}