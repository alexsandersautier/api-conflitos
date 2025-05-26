<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InqueritoPolicial extends Model
{
    use HasFactory;

    protected $table = 'inquerito_policial';
    protected $primaryKey = 'idInqueritoPolicial';

    protected $fillable = [
        'idConflito',
        'idTipoInqueritoPolicial',
        'numero_bo',
        'data',
        'assistencia_juridica'
    ];

    public function conflito() {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
    
    public function tipo_inquerito_policial() {
        return $this->belongsTo(TipoInqueritoPolicial::class, 'idTipoInqueritoPolicial');
    }
}
