<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ator extends Model
{
    use HasFactory;

    protected $table = 'ator';
    protected $primaryKey = 'idAtor';

    protected $fillable = [
        'idTipoAtor',
        'idConflito',
        'nome'
    ];

    public function tipo_ator()
    {
        return $this->belongsTo(TipoAtor::class, 'idTipoAtor');
    }
    
    public function conflito()
    {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
}
