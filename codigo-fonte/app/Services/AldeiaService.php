<?php

namespace App\Services;

use App\Models\Aldeia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class AldeiaService
{
    const CACHE_TTL = 3600;
    const CACHE_PREFIX = 'aldeias_';
    const CHUNK_SIZE = 500; // Processar em lotes
    
    /**
     * Retorna todas as aldeias com cache - OTIMIZADO
     */
    public function getAllAldeias(): Collection
    {
        $cacheKey = self::CACHE_PREFIX . 'all';
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            Log::info('Cache miss - Buscando todas as aldeias do banco (otimizado)');
            
            // Seleciona apenas colunas necessárias
            return Aldeia::select([
                'idAldeia',
                 'nm_uf',
                 'nm_munic',
                'nome'
//                 ,
//                 'situacao',
//                 'fase',
//                 'amz_leg',
//                 'lat',
//                 'long'
            ])
            ->orderBy('nome')
            ->get();
        });
    }
    
    /**
     * Retorna lista paginada de aldeias com cache - OTIMIZADO
     */
    public function getAldeiasPaginated(int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = self::CACHE_PREFIX . "paginated_{$perPage}_" . request()->get('page', 1);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($perPage) {
            Log::info('Cache miss - Buscando aldeias paginadas do banco (otimizado)');
            
            return Aldeia::select([
                'idAldeia',
//                 'nm_uf',
//                 'nm_munic',
                'nome'
//                 ,
//                 'situacao',
//                 'fase',
//                 'amz_leg',
//                 'lat',
//                 'long'
            ])
            ->orderBy('nome')
            ->paginate($perPage);
        });
    }
    
    /**
     * Retorna estatísticas das aldeias com cache - OTIMIZADO
     */
    public function getEstatisticas(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'estatisticas';
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            Log::info('Cache miss - Buscando estatísticas das aldeias do banco (otimizado)');
            
            // Usa agregados do banco em vez de carregar todos os dados
            $total = Aldeia::count();
            
            $porUf = Aldeia::select('nm_uf', \DB::raw('COUNT(*) as total'))
            ->groupBy('nm_uf')
            ->orderBy('total', 'DESC')
            ->get()
            ->pluck('total', 'nm_uf')
            ->toArray();
            
            $porMunicipio = Aldeia::select('nm_munic', \DB::raw('COUNT(*) as total'))
            ->groupBy('nm_munic')
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
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($nome) {
            Log::info("Cache miss - Buscando aldeias com nome {$nome} do banco (otimizado)");
            
            return Aldeia::select([
                'idAldeia',
                'nome',
                'cd_uf',
                'nm_uf',
                'nm_munic',
                'situacao'
            ])
            ->where('nome', 'LIKE', "{$nome}%") // Busca por prefixo (mais eficiente)
            ->orderBy('nome')
            ->limit(100) // Limite para evitar sobrecarga
            ->get();
        });
    }
    
    /**
     * Busca avançada com cache - OTIMIZADO
     */
    public function buscarAldeias(array $filtros): Collection
    {
        $cacheKey = self::CACHE_PREFIX . 'busca_' . md5(serialize($filtros));
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filtros) {
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
            ->limit(200) // Limite para evitar sobrecarga
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
        ])
        ->orderBy('idAldeia')
        ->chunk(self::CHUNK_SIZE, function ($aldeias) use ($callback) {
            $callback($aldeias);
            
            // Limpa memória após cada lote
            gc_collect_cycles();
        });
    }
    
    /**
     * Retorna apenas IDs e nomes (para selects) - MUITO LEVE
     */
    public function getAldeiasMinimal(): Collection
    {
        $cacheKey = self::CACHE_PREFIX . 'minimal';
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return Aldeia::select('idAldeia', 'nome')
            ->orderBy('nome')
            ->get();
        });
    }
}