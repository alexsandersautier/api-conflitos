<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiderancaAmeacada extends Model
{
    use HasFactory;

    protected $table = 'lideranca_ameacada';
    protected $primaryKey = 'idLiderancaAmeacada';

    protected $fillable = [
        'idConflito',
        'nome',
        'distancia_conflito'
    ];

    public function conflito()
    {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
}
