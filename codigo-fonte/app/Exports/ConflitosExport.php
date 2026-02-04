<?php
namespace App\Exports;

use App\Models\Conflito;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ConflitosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{

    private $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        if (! Auth::guard('sanctum')->check()) {
            abort(Response::HTTP_UNAUTHORIZED, 'Não autorizado');
        }

        try {
            $query = Conflito::with([
                'aldeias',
                'assuntos',
                'atoresIdentificados',
                'categoriasAtores',
                'impactosAmbientais',
                'impactosSaude',
                'impactosSocioEconomicos',
                'inqueritos',
                'localidadesConflito',
                'numerosSeiIdentificacaoConflito',
                'povos',
                'processosJudiciais',
                'programasProtecao',
                'terrasIndigenas',
                'tiposConflito',
                'violenciasPatrimoniais',
                'violenciasPessoasIndigenas',
                'violenciasPessoasNaoIndigenas'
            ]);
            
            
            // Aplicar filtros se fornecidos
            if (! empty($this->filters['search'])) {
                $query->where('nome', 'LIKE', "%{$this->filters['search']}%");
            }

            if (! empty($this->filters['estrategiaGeralUtilizadaDemed'])) {
                $query->where('estrategiaGeralUtilizadaDemed', '=', $this->filters['estrategiaGeralUtilizadaDemed']);
            }

            // FILTRO POR POVO - CORRIGIDO (com qualificação de tabela)
            $povo = $this->filters['povo'] ?? null;
            if (!empty($povo)) {
                $query->whereHas('povos', function($q) use ($povo) {
                    // Qualifique a coluna com o nome da tabela (povo)
                    $q->where('povo.idPovo', '=', $povo);
                });
            }
            
            // FILTRO POR TERRAS INDÍGENAS
            $terraIndigena = $this->filters['terraIndigena'] ?? null;
            
            if (!empty($terraIndigena)) {
                $query->whereHas('terrasIndigenas', function($q) use ($terraIndigena){
                    $q->where('terra_indigena.idTerraIndigena', '=', $terraIndigena);
                });
            }
            
            // FILTRO POR VIOLÊNCIAS CONTRA PESSOAS INDÍGENAS
            $tipoViolenciaIndigena = $this->filters['tipoViolenciaIndigena'] ?? null;
            if (!empty($tipoViolenciaIndigena)) {
                $query->whereHas('violenciasPessoasIndigenas', function($q) use ($tipoViolenciaIndigena) {
                    $q->where('violencia_pessoa_indigena.tipoViolencia', '=', $tipoViolenciaIndigena);
                });
            }
            
            if (! empty($this->filters['status'])) {
                $query->where('status', '=', $this->filters['status']);
            }

            if (! empty($this->filters['created_by'])) {
                $query->where('created_by', '=', $this->filters['created_by']);
            }

            if (! empty($this->filters['sort_by'])) {
                $sortOrder = $this->filters['sort_order'] ?? 'desc';
                $query->orderBy($this->filters['sort_by'], $sortOrder);
            } else {
                $query->orderBy('dataInicioConflito', 'desc');
            }
            $conflitos = $query->get();
            
            Log::info('Conflitos da planilha:', ['Conflitos' => $conflitos]);
            
            return $conflitos;
        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Erro ao exportar conflitos: ' . $e->getMessage());
        }
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Data Início',
            'Data Acionamento MPI',
            'Status',
            'Classificação Gravidade',
            'Estratégia DEMED',
            'Latitude',
            'Longitude',
            'Terras Indígenas',
            'Povos',
            'Assuntos',
            'Tipos de Conflito',
            'Impactos Ambientais',
            'Impactos Saúde',
            'Impactos Socioeconômicos',
            'Atores Identificados',
            'Categorias de Atores',
            'Inquéritos',
            'Processos SEI',
            'Processos Judiciais',
            'Programas de Proteção',
            'Violências Patrimoniais',
            'Violências Pessoas Indígenas',
            'Violências Pessoas Não Indígenas',
            'Localidades',
            'Região Prioritária',
            'Membro Programa Proteção',
            'Assistência Jurídica',
            'Tipo Instituição Jurídica',
            'Advogado Instituição',
            'Observações',
            'Criado Por',
            'Atualizado Por',
            'Revisado Por',
            'Data Criação',
            'Data Atualização',
            'Data Revisão'
        ];
    }

    public function map($conflito): array
    {
        return [
            $conflito->idConflito,
            $conflito->nome,
            $this->formatDate($conflito->dataInicioConflito),
            $this->formatDate($conflito->dataAcionamentoMpiConflito),
            $conflito->status,
            $conflito->classificacaoGravidadeConflitoDemed,
            $conflito->estrategiaGeralUtilizadaDemed,
            $conflito->latitude,
            $conflito->longitude,
            $this->formatRelacionamentos($conflito->terrasIndigenas, 'nome'),
            $this->formatRelacionamentos($conflito->povos, 'nome'),
            $this->formatRelacionamentos($conflito->assuntos, 'nome'),
            $this->formatRelacionamentos($conflito->tiposConflito, 'nome'),
            $this->formatRelacionamentos($conflito->impactosAmbientais, 'nome'),
            $this->formatRelacionamentos($conflito->impactosSaude, 'nome'),
            $this->formatRelacionamentos($conflito->impactosSocioEconomicos, 'nome'),
            $this->formatRelacionamentos($conflito->atoresIdentificados, 'nome'),
            $this->formatRelacionamentos($conflito->categoriasAtores, 'nome'),
            $this->formatInqueritos($conflito->inqueritos),
            $this->formatNumerosSei($conflito->numerosSeiIdentificacaoConflito),
            $this->formatProcessosJudiciais($conflito->processosJudiciais),
            $this->formatProgramasProtecao($conflito->programasProtecao),
            $this->formatViolenciasPatrimoniais($conflito->violenciasPatrimoniais),
            $this->formatViolenciasPessoasIndigenas($conflito->violenciasPessoasIndigenas),
            $this->formatViolenciasPessoasNaoIndigenas($conflito->violenciasPessoasNaoIndigenas),
            $this->formatLocalidades($conflito->localidadesConflito),
            $conflito->regiaoPrioritaria,
            $conflito->flagHasMembroProgramaProtecao,
            $conflito->flagHasAssistenciaJuridica,
            $conflito->tipoInstituicaoAssistenciaJuridica,
            $conflito->advogadoInstituicaoAssistenciaJuridica,
            $conflito->observacoes,
            $conflito->created_by,
            $conflito->updated_by,
            $conflito->revised_by,
            $this->formatDateTime($conflito->created_at),
            $this->formatDateTime($conflito->updated_at),
            $this->formatDateTime($conflito->revised_at)
        ];
    }

    private function formatDate($date){
        return date('d/m/Y', strtotime($date));
    }
    
    private function formatDateTime($date){
        return date('d/m/Y h:i:s', strtotime($date));
    }
    
    private function formatRelacionamentos($data, $field)
    {
        $collection = collect($data);
        
        if ($collection->isEmpty()) {
            return '';
        }

        return $collection->pluck($field)->implode('; ');
    }

    private function formatArray($array)
    {
        if (empty($array)) {
            return '';
        }

        return implode('; ', $array);
    }

    private function formatNumerosSei($data)
    {
        $numSei= collect($data); // Converte para garantir que é Collection
        
        if ($numSei->isEmpty()) {
            return '';
        }
        
        return $numSei->map(function ($sei) {
            $sei = (object) $sei;
            return $sei->numeroSei;
        })->implode('; ');
    }
    
    private function formatInqueritos($data)
    {
        $inqueritos = collect($data); // Converte para garantir que é Collection
        
        if ($inqueritos->isEmpty()) {
            return '';
        }
        
        return $inqueritos->map(function ($inquerito) {
            $inquerito = (object) $inquerito;
            return "[{$inquerito->numero} - {$this->formatDate($inquerito->data)} - {$inquerito->orgao}]";
        })->implode('; ');
    }

    private function formatProcessosJudiciais($data)
    {
        $processos = collect($data);
        
        if ($processos->isEmpty()) {
            return '';
        }
        
        return $processos->map(function ($processo) {
            $processo = (object) $processo;
            return "[{$processo->numero} - {$processo->orgaoApoio}]";
        })->implode('; ');
    }

    private function formatProgramasProtecao($data)
    {
        $programas = collect($data);
        
        if ($programas->isEmpty()) {
            return '';
        }
        
        return $programas->map(function ($programa) {
            $programa = (object) $programa;
            return "[{$programa->tipoPrograma} - {$programa->uf}]";
        })->implode('; ');
    }

    private function formatViolenciasPatrimoniais($violencias)
    {
        if ($violencias->isEmpty()) {
            return '';
        }

        $formatted = [];
        foreach ($violencias as $violencia) {
            $formatted[] = "[{$violencia->tipoViolencia} - {$this->formatDate($violencia->data)}]";
        }

        return implode('; ', $formatted);
    }

    private function formatViolenciasPessoasIndigenas($violencias)
    {
        if ($violencias->isEmpty()) {
            return '';
        }

        $formatted = [];
        foreach ($violencias as $violencia) {
            $formatted[] = "[{$violencia->nome} - {$violencia->tipoViolencia} - {$this->formatDate($violencia->data)}]";
        }

        return implode('; ', $formatted);
    }

    private function formatViolenciasPessoasNaoIndigenas($violencias)
    {
        if ($violencias->isEmpty()) {
            return '';
        }

        $formatted = [];
        foreach ($violencias as $violencia) {
            $formatted[] = "[{$violencia->nome} - {$violencia->tipoViolencia} - {$violencia->tipoPessoa} - {$this->formatDate($violencia->data)}]";
        }

        return implode('; ', $formatted);
    }

    private function formatLocalidades($localidades)
    {
        if ($localidades->isEmpty()) {
            return '';
        }

        $formatted = [];
        foreach ($localidades as $localidade) {
            $formatted[] = "[{$localidade->municipio} - {$localidade->uf} - {$localidade->regiao}]";
        }

        return implode('; ', $formatted);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para o cabeçalho
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF'
                    ]
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => '9E0000'
                    ]
                ]
            ],
            // Congelar primeira linha
            'A2' => [
                'freezePane' => 'A2'
            ]
        ];
    }

    public function title(): string
    {
        return 'Conflitos';
    }
}