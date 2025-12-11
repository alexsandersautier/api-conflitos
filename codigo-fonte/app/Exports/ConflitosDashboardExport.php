<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ConflitosDashboardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{

    private $conflitos;

    public function __construct(Collection $conflitos)
    {
        $this->conflitos = $conflitos;
    }

    public function collection()
    {
        return $this->conflitos;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Data Início',
            'Status',
            'Classificação Gravidade',
            'Tipos de Conflito',
            'Latitude',
            'Longitude',
            'Data Criação'
        ];
    }

    public function map($conflito): array
    {
        return [
            $conflito->idConflito,
            $conflito->nome,
            $conflito->dataInicioConflito,
            $conflito->status,
            $conflito->classificacaoGravidadeConflitoDemed,
            $conflito->tiposConflito->pluck('descricao')->implode('; '),
            $conflito->latitude,
            $conflito->longitude,
            $conflito->created_at->format('d/m/Y H:i:s')
        ];
    }
}