<?php

namespace App\Http\Controllers;

use App\Services\DashboardProxy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\PathItem(
 *     path="/api/dashboard"
 * )
 *
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Endpoints para o Dashboard"
 * )
 */
class DashboardController extends Controller
{
    protected $dashboardProxy;

    public function __construct(DashboardProxy $dashboardProxy)
    {
        // Inserir timezone do brasil
        date_default_timezone_set('America/Sao_Paulo');
        $this->dashboardProxy = $dashboardProxy;
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/dados",
     *     summary="Retorna todos os dados do dashboard",
     *     description="Retorna um conjunto completo de dados para o dashboard, incluindo totais gerais, distribuição geográfica e estatísticas de violências",
     *     operationId="getDadosDashboard",
     *     tags={"Dashboard"},

     *     @OA\Response(
     *         response=200,
     *         description="Dados do dashboard recuperados com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="totais_gerais", type="object",
     *                 @OA\Property(property="total_conflitos", type="integer", example=150),
     *                 @OA\Property(property="total_terras_indigenas", type="integer", example=45),
     *                 @OA\Property(property="total_povos", type="integer", example=28),
     *                 @OA\Property(property="total_violencias", type="integer", example=325)
     *             ),
     *             @OA\Property(property="conflitos_por_ano", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="ano", type="integer", example=2025),
     *                     @OA\Property(property="total", type="integer", example=45)
     *                 )
     *             ),
     *             @OA\Property(property="conflitos_por_uf", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="uf", type="string", example="AM"),
     *                     @OA\Property(property="total", type="integer", example=32)
     *                 )
     *             ),
     *             @OA\Property(property="estatisticas_violencias", type="object",
     *                 @OA\Property(property="patrimoniais", type="integer", example=120),
     *                 @OA\Property(property="pessoas_indigenas", type="integer", example=85),
     *                 @OA\Property(property="pessoas_nao_indigenas", type="integer", example=120),
     *                 @OA\Property(property="total_geral", type="integer", example=325)
     *             ),
     *             @OA\Property(property="ultima_atualizacao", type="string", format="date-time", example="2025-10-07 15:30:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro ao recuperar dados do dashboard")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/dados-filtrados",
     *     summary="Retorna dados do dashboard com filtro por período",
     *     description="Retorna dados do dashboard filtrados por um período específico",
     *     operationId="getDadosDashboardComFiltro",
     *     tags={"Dashboard"},
     *     @OA\Parameter(
     *         name="data_inicio",
     *         in="query",
     *         required=true,
     *         description="Data de início do período (formato YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2025-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="data_fim",
     *         in="query",
     *         required=true,
     *         description="Data de fim do período (formato YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2025-10-31")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados filtrados recuperados com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="totais_gerais", type="object"),
     *             @OA\Property(property="conflitos_por_ano", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="conflitos_por_uf", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="estatisticas_violencias", type="object"),
     *             @OA\Property(property="periodo", type="object",
     *                 @OA\Property(property="data_inicio", type="string", example="2025-01-01"),
     *                 @OA\Property(property="data_fim", type="string", example="2025-10-31")
     *             ),
     *             @OA\Property(property="ultima_atualizacao", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erro de validação"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/totais-gerais",
     *     summary="Retorna apenas os totais gerais do dashboard",
     *     description="Retorna os totais gerais de conflitos, terras indígenas, povos e violências",
     *     operationId="getTotaisGerais",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Totais gerais recuperados com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_conflitos", type="integer", example=150),
     *             @OA\Property(property="total_terras_indigenas", type="integer", example=45),
     *             @OA\Property(property="total_povos", type="integer", example=28),
     *             @OA\Property(property="total_violencias", type="integer", example=325)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/distribuicao-geografica",
     *     summary="Retorna a distribuição geográfica completa",
     *     description="Retorna dados de distribuição geográfica incluindo conflitos por UF, região e top municípios",
     *     operationId="getDistribuicaoGeografica",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Distribuição geográfica recuperada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="por_uf", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="uf", type="string", example="AM"),
     *                     @OA\Property(property="total", type="integer", example=32)
     *                 )
     *             ),
     *             @OA\Property(property="por_regiao", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="regiao", type="string", example="Amazônia"),
     *                     @OA\Property(property="total", type="integer", example=45)
     *                 )
     *             ),
     *             @OA\Property(property="top_municipios", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="uf", type="string", example="AM"),
     *                     @OA\Property(property="municipio", type="string", example="São Gabriel da Cachoeira"),
     *                     @OA\Property(property="total", type="integer", example=15)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/conflitos-por-uf",
     *     summary="Retorna conflitos por UF",
     *     description="Retorna a quantidade de conflitos agrupados por Unidade Federativa",
     *     operationId="getConflitosPorUF",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Conflitos por UF recuperados com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="uf", type="string", example="AM"),
     *                 @OA\Property(property="total", type="integer", example=32)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/conflitos-por-regiao",
     *     summary="Retorna conflitos por região",
     *     description="Retorna a quantidade de conflitos agrupados por região geográfica",
     *     operationId="getConflitosPorRegiao",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Conflitos por região recuperados com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="regiao", type="string", example="Amazônia"),
     *                 @OA\Property(property="total", type="integer", example=45)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/conflitos-por-municipio",
     *     summary="Retorna top municípios com mais conflitos",
     *     description="Retorna os 10 municípios com maior número de conflitos",
     *     operationId="getConflitosPorMunicipio",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Conflitos por município recuperados com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="uf", type="string", example="AM"),
     *                 @OA\Property(property="municipio", type="string", example="São Gabriel da Cachoeira"),
     *                 @OA\Property(property="total", type="integer", example=15)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/conflitos-por-ano",
     *     summary="Retorna conflitos por ano",
     *     description="Retorna a quantidade de conflitos agrupados por ano de início",
     *     operationId="getConflitosPorAno",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Conflitos por ano recuperados com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="ano", type="integer", example=2025),
     *                 @OA\Property(property="total", type="integer", example=45)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/estatisticas-violencias",
     *     summary="Retorna estatísticas de violências",
     *     description="Retorna estatísticas detalhadas sobre violências patrimoniais, contra pessoas indígenas e não indígenas",
     *     operationId="getEstatisticasViolencias",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Estatísticas de violências recuperadas com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="patrimoniais", type="integer", example=120),
     *             @OA\Property(property="pessoas_indigenas", type="integer", example=85),
     *             @OA\Property(property="pessoas_nao_indigenas", type="integer", example=120),
     *             @OA\Property(property="total_geral", type="integer", example=325),
     *             @OA\Property(property="violencias_por_tipo", type="object",
     *                 @OA\Property(property="patrimoniais", type="object",
     *                     @OA\Property(property="Expulsão", type="integer", example=45),
     *                     @OA\Property(property="Destruição de propriedade", type="integer", example=75)
     *                 ),
     *                 @OA\Property(property="pessoas_indigenas", type="object",
     *                     @OA\Property(property="Ameaça", type="integer", example=30),
     *                     @OA\Property(property="Prisão", type="integer", example=25)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/dados-avancados",
     *     summary="Retorna dados específicos com filtros avançados",
     *     description="Retorna dados específicos do dashboard com múltiplos filtros aplicáveis",
     *     operationId="getDadosFiltradosAvancados",
     *     tags={"Dashboard"},
     *     @OA\Parameter(
     *         name="tipo_dado",
     *         in="query",
     *         required=true,
     *         description="Tipo de dado a ser retornado",
     *         @OA\Schema(
     *             type="string",
     *             enum={"totais", "geografia", "violencias", "todos"},
     *             example="geografia"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="data_inicio",
     *         in="query",
     *         required=false,
     *         description="Data de início do período (formato YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2025-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="data_fim",
     *         in="query",
     *         required=false,
     *         description="Data de fim do período (formato YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date", example="2025-10-31")
     *     ),
     *     @OA\Parameter(
     *         name="uf",
     *         in="query",
     *         required=false,
     *         description="Filtrar por UF específica",
     *         @OA\Schema(type="string", maxLength=2, example="AM")
     *     ),
     *     @OA\Parameter(
     *         name="regiao",
     *         in="query",
     *         required=false,
     *         description="Filtrar por região específica",
     *         @OA\Schema(type="string", maxLength=100, example="Amazônia")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados filtrados recuperados com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="filtros_aplicados", type="object"),
     *             @OA\Property(property="message", type="string", example="Dados filtrados recuperados com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/metricas-tempo-real",
     *     summary="Retorna métricas em tempo real (sem cache)",
     *     description="Retorna dados do dashboard com cache limpo para obter informações em tempo real",
     *     operationId="getMetricasTempoReal",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Métricas em tempo real recuperadas com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="cache", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Métricas em tempo real recuperadas com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/limpar-cache",
     *     summary="Força atualização do cache do dashboard",
     *     description="Limpa o cache do dashboard para forçar atualização dos dados na próxima requisição",
     *     operationId="clearCache",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Cache limpo com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cache do dashboard limpo com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/dashboard/health-check",
     *     summary="Health check do dashboard",
     *     description="Verifica a saúde dos serviços do dashboard (banco de dados, cache, autenticação)",
     *     operationId="healthCheck",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Health check realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="string", example="healthy"),
     *                 @OA\Property(property="timestamp", type="string", format="date-time"),
     *                 @OA\Property(property="services", type="object",
     *                     @OA\Property(property="database", type="string", example="connected"),
     *                     @OA\Property(property="cache", type="string", example="working"),
     *                     @OA\Property(property="auth", type="string", example="authenticated")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Health check realizado com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro no health check",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="status", type="string", example="unhealthy"),
     *             @OA\Property(property="message", type="string", example="Erro no health check")
     *         )
     *     )
     * )
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
     * Verifica conexão com o banco
     */
    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();
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
            Cache::put('health_check', 'ok', 10);
            return Cache::get('health_check') === 'ok' ? 'working' : 'failing';
        } catch (\Exception $e) {
            return 'failing';
        }
    }
}
