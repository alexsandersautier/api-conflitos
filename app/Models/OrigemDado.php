<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrigemDado extends Model
{
    use HasFactory;

    protected $table = 'origem_dado';
    protected $primaryKey = 'idOrigemDado';

    protected $fillable = [
        'idConflito', 'idTipoResponsavel', 'setor_cadastrante', 'observacao'
    ];

    public function conflito() {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
    
    public function tipo_responsavel() {
        return $this->belongsTo(TipoResponsavel::class, 'idTipoResponsavel');
    }
}