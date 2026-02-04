<?php
namespace App\Services;

use App\Models\Aldeia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Throwable;

class AldeiaService
{

    const CACHE_TTL = 3600;

    const CACHE_PREFIX = 'aldeias_';

    const CHUNK_SIZE = 500;

    const CACHE_RETRY_ATTEMPTS = 3;

    const CACHE_RETRY_DELAY = 100;

    // milliseconds

    /**
     * Retorna todas as aldeias com cache - OTIMIZADO
     */
    public function getAllAldeias(): Collection
    {
        $cacheKey = self::CACHE_PREFIX . 'all';

        return $this->getWithCacheRecovery($cacheKey, function () {
            return Aldeia::select([
                'idAldeia',
                'nome',
                'nm_munic',
                'nm_uf'
            ])->orderBy('nome')
                ->get()
                ->map(function ($aldeia) {
                return [
                    'idAldeia' => $aldeia->idAldeia,
                    'nome' => $aldeia->nome,
                    'nm_munic' => $aldeia->nm_munic,
                    'nm_uf' => $aldeia->nm_uf
                ];
            });
        });
    }

    /**
     * Retorna lista paginada de aldeias com cache - OTIMIZADO
     */
    public function getAldeiasPaginated(int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = self::CACHE_PREFIX . "paginated_{$perPage}_" . request()->get('page', 1);

        return $this->getWithCacheRecovery($cacheKey, function () use ($perPage) {
            return Aldeia::select([
                'idAldeia',
                'nome'
            ])->orderBy('nome')
                ->paginate($perPage);
        });
    }

    /**
     * Retorna estatísticas das aldeias com cache - OTIMIZADO
     */
    public function getEstatisticas(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'estatisticas';

        return $this->getWithCacheRecovery($cacheKey, function () {
            Log::info('Cache miss - Buscando estatísticas das aldeias do banco (otimizado)');

            $total = Aldeia::count();

            $porUf = Aldeia::select('nm_uf', DB::raw('COUNT(*) as total'))->groupBy('nm_uf')
                ->orderBy('total', 'DESC')
                ->get()
                ->pluck('total', 'nm_uf')
                ->toArray();

            $porMunicipio = Aldeia::select('nm_munic', DB::raw('COUNT(*) as total'))->groupBy('nm_munic')
                ->orderBy('total', 'DESC')
                ->limit(10)
                ->get()
                ->pluck('total', 'nm_munic')
                ->toArray();

            return [
                'total_aldeias' => $total,
                'aldeias_por_uf' => $porUf,
                'aldeias_por_municipio' => $porMunicipio,
                'ultima_atualizacao' => Aldeia::max('updated_at')
            ];
        });
    }

    /**
     * Busca aldeias por nome com cache - OTIMIZADO
     */
    public function getAldeiasByNome(string $nome): Collection
    {
        $cacheKey = self::CACHE_PREFIX . "nome_" . md5($nome);

        return $this->getWithCacheRecovery($cacheKey, function () use ($nome) {
            Log::info("Cache miss - Buscando aldeias com nome {$nome} do banco (otimizado)");

            return Aldeia::select([
                'idAldeia',
                'nome',
                'cd_uf',
                'nm_uf',
                'nm_munic',
                'situacao'
            ])->where('nome', 'LIKE', "{$nome}%")
                ->orderBy('nome')
                ->limit(100)
                ->get();
        });
    }

    /**
     * Busca avançada com cache - OTIMIZADO
     */
    public function buscarAldeias(array $filtros): Collection
    {
        $cacheKey = self::CACHE_PREFIX . 'busca_' . md5(serialize($filtros));

        return $this->getWithCacheRecovery($cacheKey, function () use ($filtros) {
            Log::info('Cache miss - Buscando aldeias com filtros do banco (otimizado)', $filtros);

            $query = Aldeia::select([
                'idAldeia',
                'nome',
                'cd_uf',
                'nm_uf',
                'nm_munic',
                'situacao'
            ]);

            if (isset($filtros['nome'])) {
                $query->where('nome', 'LIKE', "{$filtros['nome']}%");
            }

            if (isset($filtros['uf'])) {
                $query->where('cd_uf', $filtros['uf']);
            }

            if (isset($filtros['municipio'])) {
                $query->where('nm_munic', 'LIKE', "{$filtros['municipio']}%");
            }

            if (isset($filtros['situacao'])) {
                $query->where('situacao', $filtros['situacao']);
            }

            return $query->orderBy('nome')
                ->limit(200)
                ->get();
        });
    }

    /**
     * Método para dados em lote (chunk) - para grandes volumes
     */
    public function processarAldeiasEmLote(callable $callback): void
    {
        Aldeia::select([
            'idAldeia',
            'nome',
            'cd_uf',
            'nm_uf',
            'nm_munic'
        ])->orderBy('idAldeia')->chunk(self::CHUNK_SIZE, function ($aldeias) use ($callback) {
            $callback($aldeias);
            gc_collect_cycles();
        });
    }

    /**
     * Retorna apenas IDs e nomes (para selects) - MUITO LEVE
     */
    public function getAldeiasMinimal(): Collection
    {
        $cacheKey = self::CACHE_PREFIX . 'minimal';

        return $this->getWithCacheRecovery($cacheKey, function () {
            return Aldeia::select('idAldeia', 'nome')->orderBy('nome')
                ->get();
        });
    }

    /**
     * Método principal com recuperação de cache
     */
    private function getWithCacheRecovery(string $cacheKey, callable $callback, int $attempts = self::CACHE_RETRY_ATTEMPTS)
    {
        try {
            // Tenta buscar do cache primeiro
            $cachedData = Cache::get($cacheKey);

            if ($cachedData !== null) {
                return $cachedData;
            }

            // Se não encontrou no cache, executa o callback e armazena
            return $this->storeInCacheWithRetry($cacheKey, $callback, $attempts);
        } catch (Throwable $e) {
            Log::error("Erro crítico no cache para {$cacheKey}: " . $e->getMessage(), [
                'exception' => $e,
                'cache_key' => $cacheKey
            ]);

            // Em caso de erro crítico, executa sem cache como fallback
            return $this->executeWithFallback($cacheKey, $callback, $e);
        }
    }

    /**
     * Armazena no cache com tentativas de retry
     */
    private function storeInCacheWithRetry(string $cacheKey, callable $callback, int $attempts)
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt ++) {
            try {
                $data = $callback();

                // Tenta armazenar no cache
                Cache::put($cacheKey, $data, self::CACHE_TTL);

                Log::info("Cache reconstruído com sucesso: {$cacheKey} (tentativa {$attempt})");
                return $data;
            } catch (Throwable $e) {
                $lastException = $e;
                Log::warning("Tentativa {$attempt} falhou para cache {$cacheKey}: " . $e->getMessage());

                if ($attempt < $attempts) {
                    usleep(self::CACHE_RETRY_DELAY * 1000); // Converte para microsegundos
                }
            }
        }

        // Se todas as tentativas falharem, executa sem cache
        Log::error("Todas as tentativas de cache falharam para {$cacheKey}", [
            'exception' => $lastException
        ]);

        return $callback();
    }

    /**
     * Executa o callback como fallback quando o cache falha
     */
    private function executeWithFallback(string $cacheKey, callable $callback, Throwable $originalException)
    {
        try {
            Log::warning("Executando fallback sem cache para: {$cacheKey}");
            return $callback();
        } catch (Throwable $fallbackException) {
            Log::critical("Falha no fallback para {$cacheKey}", [
                'original_exception' => $originalException->getMessage(),
                'fallback_exception' => $fallbackException->getMessage()
            ]);

            throw new Exception("Falha no cache e no fallback para {$cacheKey}: " . $fallbackException->getMessage(), 0, $originalException);
        }
    }

    /**
     * Limpa cache específico ou todos os caches do serviço
     */
    public function clearCache(?string $specificKey = null): bool
    {
        try {
            if ($specificKey) {
                $cacheKey = self::CACHE_PREFIX . $specificKey;
                $deleted = Cache::forget($cacheKey);
                Log::info("Cache específico limpo: {$cacheKey}", [
                    'success' => $deleted
                ]);
                return $deleted;
            } else {
                // Limpa todos os caches do serviço
                Cache::flush();
                Log::info("Todos os caches do AldeiaService foram limpos");
                return true;
            }
        } catch (Throwable $e) {
            Log::error("Erro ao limpar cache: " . $e->getMessage(), [
                'specific_key' => $specificKey
            ]);
            return false;
        }
    }

    /**
     * Reconstroi forçadamente um cache específico
     */
    public function rebuildCache(string $cacheType, ...$params): bool
    {
        try {
            $methodMap = [
                'all' => 'getAllAldeias',
                'estatisticas' => 'getEstatisticas',
                'minimal' => 'getAldeiasMinimal'
            ];

            if (! isset($methodMap[$cacheType])) {
                throw new Exception("Tipo de cache inválido: {$cacheType}");
            }

            $method = $methodMap[$cacheType];

            // Limpa o cache existente primeiro
            $this->clearCache($cacheType);

            // Força a reconstrução chamando o método
            call_user_func_array([
                $this,
                $method
            ], $params);

            Log::info("Cache reconstruído forçadamente: {$cacheType}");
            return true;
        } catch (Throwable $e) {
            Log::error("Erro ao reconstruir cache {$cacheType}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica saúde do cache
     */
    public function getCacheHealth(): array
    {
        $testKeys = [
            'minimal' => self::CACHE_PREFIX . 'minimal',
            'estatisticas' => self::CACHE_PREFIX . 'estatisticas'
        ];

        $health = [];

        foreach ($testKeys as $name => $key) {
            try {
                $exists = Cache::has($key);
                $health[$name] = [
                    'exists' => $exists,
                    'key' => $key
                ];
            } catch (Throwable $e) {
                $health[$name] = [
                    'exists' => false,
                    'error' => $e->getMessage(),
                    'key' => $key
                ];
            }
        }

        return $health;
    }
}