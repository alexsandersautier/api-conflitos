<?php

namespace App\Observers;

use App\Models\Aldeia;
use App\Services\AldeiaService;
use Illuminate\Support\Facades\Log;

class AldeiaObserver
{
    protected AldeiaService $aldeiaService;
    
    public function __construct(AldeiaService $aldeiaService)
    {
        $this->aldeiaService = $aldeiaService;
    }
    
    /**
     * Handle the Aldeia "created" event.
     */
    public function created(Aldeia $aldeia): void
    {
        Log::info("Aldeia criada - Limpando caches relacionados", [
            'idAldeia' => $aldeia->idAldeia,
            'nome' => $aldeia->nome
        ]);
        
        $this->clearRelevantCaches();
    }
    
    /**
     * Handle the Aldeia "updated" event.
     */
    public function updated(Aldeia $aldeia): void
    {
        Log::info("Aldeia atualizada - Limpando caches relacionados", [
            'idAldeia' => $aldeia->idAldeia,
            'nome' => $aldeia->nome,
            'campos_alterados' => $aldeia->getChanges()
        ]);
        
        $this->clearRelevantCaches();
        
        // Se o nome foi alterado, limpar caches de busca específicos
        if (array_key_exists('nome', $aldeia->getChanges())) {
            $this->clearSearchRelatedCaches();
        }
    }
    
    /**
     * Handle the Aldeia "deleted" event.
     */
    public function deleted(Aldeia $aldeia): void
    {
        Log::info("Aldeia deletada - Limpando caches relacionados", [
            'idAldeia' => $aldeia->idAldeia,
            'nome' => $aldeia->nome
        ]);
        
        $this->clearRelevantCaches();
    }
    
    /**
     * Handle the Aldeia "restored" event.
     */
    public function restored(Aldeia $aldeia): void
    {
        Log::info("Aldeia restaurada - Limpando caches relacionados", [
            'idAldeia' => $aldeia->idAldeia,
            'nome' => $aldeia->nome
        ]);
        
        $this->clearRelevantCaches();
    }
    
    /**
     * Handle the Aldeia "force deleted" event.
     */
    public function forceDeleted(Aldeia $aldeia): void
    {
        Log::info("Aldeia force deleted - Limpando caches relacionados", [
            'idAldeia' => $aldeia->idAldeia,
            'nome' => $aldeia->nome
        ]);
        
        $this->clearRelevantCaches();
    }
    
    /**
     * Limpa todos os caches relevantes após operações CRUD
     */
    private function clearRelevantCaches(): void
    {
        try {
            $cacheTypes = ['all', 'estatisticas', 'minimal'];
            
            foreach ($cacheTypes as $cacheType) {
                $this->aldeiaService->clearCache($cacheType);
            }
            
            // Limpa cache paginado (mais complexo - precisa limpar múltiplas páginas)
            $this->clearPaginatedCaches();
            
            Log::info("Caches do AldeiaService limpos com sucesso");
            
        } catch (\Throwable $e) {
            Log::error("Erro ao limpar caches do AldeiaService: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }
    
    /**
     * Limpa caches específicos de busca quando o nome é alterado
     */
    private function clearSearchRelatedCaches(): void
    {
        try {
            // Aqui você poderia implementar lógica específica para limpar
            // caches de busca baseados em padrões de chave
            // Por enquanto, vamos limpar todos os caches para garantir
            $this->aldeiaService->clearCache();
            
            Log::info("Caches de busca limpos devido a alteração no nome");
            
        } catch (\Throwable $e) {
            Log::error("Erro ao limpar caches de busca: " . $e->getMessage());
        }
    }
    
    /**
     * Limpa caches paginados (aproximação - na prática seria melhor com tags de cache)
     */
    private function clearPaginatedCaches(): void
    {
        try {
            // Em um sistema real com tags de cache, isso seria mais eficiente
            // Por enquanto, vamos limpar todos os caches ou implementar uma estratégia
            // baseada no padrão de chaves conhecido
            
            // Estratégia: Limpar os primeiros 20 páginas como aproximação
            $perPages = [10, 15, 20, 50];
            
            foreach ($perPages as $perPage) {
                for ($page = 1; $page <= 20; $page++) {
                    $cacheKey = "paginated_{$perPage}_{$page}";
                    $this->aldeiaService->clearCache($cacheKey);
                }
            }
            
        } catch (\Throwable $e) {
            Log::warning("Erro ao limpar caches paginados: " . $e->getMessage());
        }
    }
    
    /**
     * Reconstroi caches críticos de forma assíncrona (para uso em filas)
     */
    public function rebuildCriticalCaches(): void
    {
        try {
            $criticalCaches = ['estatisticas', 'minimal', 'all'];
            
            foreach ($criticalCaches as $cacheType) {
                $this->aldeiaService->rebuildCache($cacheType);
            }
            
            Log::info("Caches críticos reconstruídos com sucesso");
            
        } catch (\Throwable $e) {
            Log::error("Erro ao reconstruir caches críticos: " . $e->getMessage());
        }
    }
}