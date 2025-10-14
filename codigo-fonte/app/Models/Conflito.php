<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conflito extends Model
{
    use HasFactory;
    
    protected $table = 'conflito';
    
    protected $primaryKey = 'idConflito';
    
    protected $fillable = [ 'latitude',
                            'longitude',
                            'nome',
                            'relato',
                            'dataInicioConflito',
                            'dataAcionamentoMpiConflito',
                            'observacoes',
                            'flagHasImpactoAmbiental',
                            'flagHasImpactoSaude',
                            'flagHasImpactoSocioEconomico',
                            'flagHasViolenciaIndigena',
                            'flagHasMembroProgramaProtecao',
                            'flagHasBOouNF',
                            'flagHasInquerito',
                            'flagHasProcessoJudicial',
                            'flagHasAssistenciaJuridica',
                            'flagHasRegiaoPrioritaria',
                            'flagHasViolenciaPatrimonialIndigena',
                            'flagHasEventoViolenciaIndigena',
                            'flagHasAssassinatoPrisaoNaoIndigena',
                            'tipoInstituicaoAssistenciaJuridica',
                            'advogadoInstituicaoAssistenciaJuridica',
                            'regiaoPrioritaria',
                            'classificacaoGravidadeConflitoDemed',
                            'atualizacaoClassificacaoGravidadeConflito',
                            'dataReferenciaMudancaClassificacao',
                            'estrategiaGeralUtilizadaDemed',
                            'estrategiaColetiva'];
    
    protected $casts = ['created_at' => 'datetime',
                        'updated_at' => 'datetime'];
    
    /**
     * Accessor para numerosSeiIdentificacaoConflito
     */
    public function getNumerosSeiIdentificacaoConflitoAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }
    
    /**
     * Mutator para numerosSeiIdentificacaoConflito
     */
    public function setNumerosSeiIdentificacaoConflitoAttribute($value)
    {
        $this->attributes['numerosSeiIdentificacaoConflito'] = is_array($value) ? json_encode($value) : $value;
    }
    
    /**
     * Relacionamento muitos-para-muitos com Aldeias
     */
    public function aldeias(): BelongsToMany
    {
        return $this->belongsToMany(Aldeia::class,'aldeia_conflito','idConflito','idAldeia')->withTimestamps();
    }
    
    /**
     * Relacionamento muitos-para-muitos com Assuntos
     */
    public function assuntos(): BelongsToMany
    {
        return $this->belongsToMany(Assunto::class,'assunto_conflito','idConflito','idAssunto')->withTimestamps();
    }
    
    /**
     * Relacionamento muitos-para-muitos com Terras Indígenas
     */
    public function terrasIndigenas(): BelongsToMany
    {
        return $this->belongsToMany(TerraIndigena::class,'terra_indigena_conflito','idConflito','idTerraIndigena')->withTimestamps();
    }
    
    /**
     * Relacionamento muitos-para-muitos com Povos
     */
    public function povos(): BelongsToMany
    {
        return $this->belongsToMany(Povo::class,'povo_conflito','idConflito','idPovo')->withTimestamps();
    }
    
    /**
     * Relacionamento muitos-para-muitos com Tipos de Conflito
     */
    public function tiposConflito(): BelongsToMany
    {
        return $this->belongsToMany(TipoConflito::class,'conflito_tipo_conflito','idConflito','idTipoConflito')->withTimestamps();
    }
    
    /**
     * Relacionamento muitos-para-muitos com Categorias de Atores
     */
    public function categoriasAtores(): BelongsToMany
    {
        return $this->belongsToMany(CategoriaAtor::class,'categoria_ator_conflito','idConflito','idCategoriaAtor')->withTimestamps();
    }
    
    
    /**
     * Relacionamento muitos-para-muitos com Impactos Ambientais
     */
    public function impactosAmbientais(): BelongsToMany
    {
        return $this->belongsToMany(ImpactoAmbiental::class,'impacto_ambiental_conflito','idConflito','idImpactoAmbiental')->withTimestamps();
    }
    
    /**
     * Relacionamento muitos-para-muitos com Impactos na Saúde
     */
    public function impactosSaude(): BelongsToMany
    {
        return $this->belongsToMany(ImpactoSaude::class,'impacto_saude_conflito','idConflito','idImpactoSaude')->withTimestamps();
    }
    
    /**
     * Relacionamento muitos-para-muitos com Impactos Socioeconômicos
     */
    public function impactosSocioEconomicos(): BelongsToMany
    {
        return $this->belongsToMany(ImpactoSocioEconomico::class,'impacto_socio_economico_conflito','idConflito','idImpactoSocioEconomico')->withTimestamps();
    }
    
    /**
     * Relacionamento um-para-muitos com atores identificados
     */
    public function atoresIdentificados(): HasMany
    {
        return $this->hasMany(AtorIdentificadoConflito::class, 'idConflito');
    }
    
    /**
     * Relacionamento um-para-muitos com inqueritos
     */
    public function inqueritos(): HasMany
    {
        return $this->hasMany(Inquerito::class, 'idConflito');
    }
    
    /**
     * Relacionamento um-para-muitos com IdentificacaoConflito
     */
    public function localidadesConflito(): HasMany
    {
        return $this->hasMany(LocalidadeConflito::class, 'idConflito');
    }
    
    /**
     * Relacionamento um-para-muitos com IdentificacaoConflito
     */
    public function numerosSeiIdentificacaoConflito(): HasMany
    {
        return $this->hasMany(NumeroSeiIdentificacaoConflito::class, 'idConflito');
    }
    
    /**
     * Relacionamento um-para-muitos com inqueritos
     */
    public function processosJudiciais(): HasMany
    {
        return $this->hasMany(ProcessoJudicial::class, 'idConflito');
    }
    
    /**
     * Relacionamento um-para-muitos com programas de protecao
     */
    public function programasProtecao(): HasMany
    {
        return $this->hasMany(ProgramaProtecao::class, 'idConflito');
    }
    
    /**
     * Relacionamento um-para-muitos com registros BO ou NF
     */
    public function registrosBOouNF(): HasMany
    {
        return $this->hasMany(RegistroBoNf::class, 'idConflito');
    }
    

    /**
     * Relacionamento um-para-muitos com Violências Patrimoniais
     */
    public function violenciasPatrimoniais(): HasMany
    {
        return $this->hasMany(ViolenciaPatrimonial::class, 'idConflito');
    }
    
    /**
     * Relacionamento um-para-muitos com Violências contra Pessoas Indígenas
     */
    public function violenciasPessoasIndigenas(): HasMany
    {
        return $this->hasMany(ViolenciaPessoaIndigena::class, 'idConflito');
    }
    
    /**
     * Relacionamento um-para-muitos com Violências contra Pessoas Não Indígenas
     */
    public function violenciasPessoasNaoIndigenas(): HasMany
    {
        return $this->hasMany(ViolenciaPessoaNaoIndigena::class, 'idConflito');
    }
    
    /**
     * Scope para conflitos com impacto ambiental
     */
    public function scopeComImpactoAmbiental($query)
    {
        return $query->where('flagHasImpactoAmbiental', 'SIM');
    }
    
    /**
     * Scope para conflitos com violência indígena
     */
    public function scopeComViolenciaIndigena($query)
    {
        return $query->where('flagHasViolenciaIndigena', 'SIM');
    }
    
    /**
     * Scope para conflitos por região
     */
    public function scopePorRegiao($query, $regiao)
    {
        return $query->where('regiao', $regiao);
    }
    
    /**
     * Scope para conflitos por UF
     */
    public function scopePorUf($query, $uf)
    {
        return $query->where('uf', $uf);
    }
    
    /**
     * Scope para conflitos por município
     */
    public function scopePorMunicipio($query, $municipio)
    {
        return $query->where('municipio', $municipio);
    }
    
    /**
     * Scope para conflitos por data de início
     */
    public function scopePorDataInicio($query, $dataInicio, $dataFim = null)
    {
        if ($dataFim) {
            return $query->whereBetween('dataInicioConflito', [$dataInicio, $dataFim]);
        }
        return $query->where('dataInicioConflito', $dataInicio);
    }
    
    /**
     * Verifica se o conflito tem violências patrimoniais
     */
    public function hasViolenciasPatrimoniais(): bool
    {
        return $this->violenciasPatrimoniais->isNotEmpty();
    }
    
    /**
     * Verifica se o conflito tem violências contra pessoas indígenas
     */
    public function hasViolenciasPessoasIndigenas(): bool
    {
        return $this->violenciasPessoasIndigenas->isNotEmpty();
    }
    
    /**
     * Verifica se o conflito tem violências contra pessoas não indígenas
     */
    public function hasViolenciasPessoasNaoIndigenas(): bool
    {
        return $this->violenciasPessoasNaoIndigenas->isNotEmpty();
    }
    
    /**
     * Retorna todos os números SEI relacionados ao conflito
     */
    public function getAllNumerosSei(): array
    {
        $numerosSei = $this->numerosSeiIdentificacaoConflito;
        
        // Adiciona números SEI das violências patrimoniais
        foreach ($this->violenciasPatrimoniais as $violencia) {
            if (!empty($violencia->numeroSei)) {
                $numerosSei[] = $violencia->numeroSei;
            }
        }
        
        // Adiciona números SEI das violências contra pessoas indígenas
        foreach ($this->violenciasPessoasIndigenas as $violencia) {
            if (!empty($violencia->numeroSei)) {
                $numerosSei[] = $violencia->numeroSei;
            }
        }
        
        // Adiciona números SEI das violências contra pessoas não indígenas
        foreach ($this->violenciasPessoasNaoIndigenas as $violencia) {
            if (!empty($violencia->numeroSei)) {
                $numerosSei[] = $violencia->numeroSei;
            }
        }
        
        // Remove duplicados e valores vazios
        return array_unique(array_filter($numerosSei));
    }
    
    /**
     * Retorna os tipos de conflito como array de strings
     */
    public function getTiposConflitoNomes(): array
    {
        return $this->tiposConflito->pluck('nome')->toArray();
    }
    
    /**
     * Retorna os nomes dos povos envolvidos
     */
    public function getPovosNomes(): array
    {
        return $this->povos->pluck('nome')->toArray();
    }
    
    /**
     * Retorna os nomes das terras indígenas envolvidas
     */
    public function getTerrasIndigenasNomes(): array
    {
        return $this->terrasIndigenas->pluck('nome')->toArray();
    }
    
    /**
     * Boot do model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Evento para deletar relacionamentos um-para-muitos
        static::deleting(function ($conflito) {
            $conflito->violenciasPatrimoniais()->delete();
            $conflito->violenciasPessoasIndigenas()->delete();
            $conflito->violenciasPessoasNaoIndigenas()->delete();
        });
    }
}