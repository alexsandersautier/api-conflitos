<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoSei extends Model
{
    use HasFactory;

    protected $table = 'processo_sei';
    protected $primaryKey = 'idProcessoSei';

    protected $fillable = [
        'idTipoProcessoSei', 'idConflito', 'numero', 'assunto', 'especificacao', 'interessado'
    ];

    // Relacionamento com Orgão
    public function tipo_processo_sei() {
        return $this->belongsTo(TipoProcessoSei::class, 'idTipoProcessoSei');
    }
    
    public function conflito() {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
}
