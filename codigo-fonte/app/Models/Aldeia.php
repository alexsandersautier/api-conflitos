<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aldeia extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'aldeia';
    protected $primaryKey = 'idAldeia';
    
    protected $fillable = [
        'nome',
        'descricao',
        'populacao',
        'area',
        'regiao',
        'ativa',
        'data_fundacao'
    ];
    
    protected $casts = [
        'ativa' => 'boolean',
        'data_fundacao' => 'date',
        'area' => 'decimal:2',
        'populacao' => 'integer'
    ];
    
    protected $dates = [
        'data_fundacao',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    // Escopo para aldeias ativas
    public function scopeAtiva($query)
    {
        return $query->where('ativa', true);
    }
    
    // Escopo para buscar por região
    public function scopeDaRegiao($query, $regiao)
    {
        return $query->where('regiao', $regiao);
    }
    
    // Relacionamentos (exemplo)
    // public function habitantes()
    // {
    //     return $this->hasMany(Habitante::class);
    // }
}