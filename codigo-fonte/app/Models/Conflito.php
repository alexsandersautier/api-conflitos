<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conflito extends Model
{
    use HasFactory;

    protected $table = 'conflito';
    protected $primaryKey = 'idConflito';
    
    protected $fillable = ['nome',
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

    public function terras_indigenas() {
        return $this->belongsToMany(TerraIndigena::class, 'terra_indigena_conflito', 'idConflito', 'idTerraIndigena');
    }

    public function povos() {
        return $this->belongsToMany(Povo::class, 'povo_conflito', 'idConflito', 'idPovo');
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
}
