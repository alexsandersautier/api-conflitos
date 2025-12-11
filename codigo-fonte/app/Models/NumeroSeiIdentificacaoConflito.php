<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumeroSeiIdentificacaoConflito extends Model
{

    protected $table = 'numero_sei_identificacao_conflito';

    protected $primaryKey = 'idNumeroSeiIdentificacaoConflito';

    protected $fillable = [
        'idConflito',
        'numeroSei'
    ];

    public $timestamps = false;

    public function conflito()
    {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
}
