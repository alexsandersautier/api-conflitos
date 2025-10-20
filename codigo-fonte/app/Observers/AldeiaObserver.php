<?php

namespace App\Observers;

use App\Models\Aldeia;
use App\Services\AldeiaService;

class AldeiaObserver
{
    protected $aldeiaService;

    public function __construct(AldeiaService $aldeiaService)
    {
        $this->aldeiaService = $aldeiaService;
    }

    public function saved(Aldeia $aldeia)
    {
        $this->aldeiaService->clearAldeiaCache($aldeia->idAldeia);
    }

    public function deleted(Aldeia $aldeia)
    {
        $this->aldeiaService->clearAldeiaCache($aldeia->idAldeia);
    }

    public function restored(Aldeia $aldeia)
    {
        $this->aldeiaService->clearAldeiaCache($aldeia->idAldeia);
    }
}