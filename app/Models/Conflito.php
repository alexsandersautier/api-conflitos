<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conflito extends Model
{
    use HasFactory;

    protected $table = 'conflito';
    protected $primaryKey = 'idConflito';
    
    protected $fillable = ['idTerraIndigena',
                            'idPovo',
                            'nome',
                            'descricao',
                            'regiao',
                            'dataInicioConflito',
                            'dataFimConflito',
                            'latitude',
                            'longitude',
                            'municipio',
                            'uf',
                            'flagOcorrenciaAmeaca',
                            'flagOcorrenciaViolencia',
                            'flagOcorrenciaAssassinato',
                            'flagOcorrenciaFeridos',
                            'flagMembroProgramaProtecao'];

    public function terra_indigena() {
        return $this->belongsTo(TerraIndigena::class, 'idTerraIndigena');
    }

    public function povo() {
        return $this->belongsTo(Povo::class, 'idPovo');
    }

    public function assuntos(){
        return $this->belongsToMany(Assunto::class, 'assunto_conflito', 'idConflito', 'idAssunto');
    }

    public function tiposconflito(){
        return $this->belongsToMany(TipoConflito::class, 'conflito_tipo_conflito', 'idConflito', 'idTipoConflito');
    }

    public function impactosambientais(){
        return $this->belongsToMany(ImpactoAmbiental::class, 'impacto_ambiental_conflito', 'idConflito', 'idImpactoAmbiental');
    }

    public function impactossaude(){
        return $this->belongsToMany(ImpactoSaude::class, 'impacto_saude_conflito', 'idConflito', 'idImpactoSaude');
    }

    public function impactosSocioEconomicos(){
        return $this->belongsToMany(ImpactoSocioEconomico::class, 'impacto_socio_economico_conflito', 'idConflito', 'idImpactoSocioEconomico');
    }
    
    public function processosSei(){
        return $this->belongsToMany(ProcessoSei::class, 'processo_sei_conflito', 'idConflito', 'idProcessoSei');
    }
    
    public function inqueritosPoliciais(){
        return $this->belongsToMany(TipoInqueritoPolicial::class, 'inquerito_policial', 'idConflito', 'idTipoInqueritoPolicial');
    }
    
    public function tiposAtores(){
        return $this->belongsToMany(TipoInqueritoPolicial::class, 'tipo_ator_conflito', 'idConflito', 'idTipoAtor');
    }
}
