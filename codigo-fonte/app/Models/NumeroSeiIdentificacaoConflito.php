<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NumeroSeiIdentificacaoConflito extends Model
{
    use HasFactory;

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
