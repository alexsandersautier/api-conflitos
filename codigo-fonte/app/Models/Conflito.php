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
                           'relato',
                           'processoSei',
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
    
    public function assuntos(){
        return $this->belongsToMany(Assunto::class, 'assunto_conflito', 'idConflito', 'idAssunto');
    }

    public function impactos_ambientais(){
        return $this->belongsToMany(ImpactoAmbiental::class, 'impacto_ambiental_conflito', 'idConflito', 'idImpactoAmbiental');
    }
    
    public function impactos_saude(){
        return $this->belongsToMany(ImpactoSaude::class, 'impacto_saude_conflito', 'idConflito', 'idImpactoSaude');
    }
    
    public function impactos_socio_economicos(){
        return $this->belongsToMany(ImpactoSocioEconomico::class, 'impacto_socio_economico_conflito', 'idConflito', 'idImpactoSocioEconomico');
    }
    
    public function povos() {
        return $this->belongsToMany(Povo::class, 'povo_conflito', 'idConflito', 'idPovo');
    }
    
    public function terras_indigenas() {
        return $this->belongsToMany(TerraIndigena::class, 'terra_indigena_conflito', 'idConflito', 'idTerraIndigena');
    }
    
    public function tipos_conflito(){
        return $this->belongsToMany(TipoConflito::class, 'conflito_tipo_conflito', 'idConflito', 'idTipoConflito');
    }
    
}
