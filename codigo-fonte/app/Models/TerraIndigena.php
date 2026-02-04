<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerraIndigena extends Model
{
    use HasFactory;

    protected $table = 'terra_indigena';

    protected $primaryKey = 'idTerraIndigena';

    protected $fillable = [
        'idPovo',
        'idSituacaoFundiaria',
        'codigo_ti',
        'nome',
        'superficie_perimetro_ha',
        'modalidade_ti',
        'etnia_nome',
        'municipio_nome',
        'uf_sigla',
        'coordenacao_regional',
        'faixa_fronteira',
        'undadm_codigo',
        'undadm_nome',
        'undadm_sigla',
        'data_atualizacao',
        'data_homologacao',
        'decreto_homologacao',
        'data_regularizacao',
        'matricula_regularizacao',
        'acao_recuperacao_territorial',
        'dominio_uniao',
        'numero_processo_funai',
        'data_abertura_processo_funai',
        'numero_portaria_funai',
        'numero_processo_sei',
        'numero_portaria_declaratoria',
        'qtd_aldeias',
        'qtd_familias',
        'links_documentos_vinculados'
    ];

    // Relacionamento com SituacaoFundiaria
    public function situacao_fundiaria()
    {
        return $this->belongsTo(SituacaoFundiaria::class, 'idSituacaoFundiaria');
    }

    // Relacionamento com Povo
    public function povo()
    {
        return $this->belongsTo(Povo::class, 'idPovo');
    }
}
