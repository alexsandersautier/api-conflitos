<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolenciaPessoaIndigena extends Model
{
    use HasFactory;

    protected $table = 'violencia_pessoa_indigena';

    protected $primaryKey = 'idViolenciaPessoaIndigena';

    protected $fillable = [
        'idConflito',
        'tipoViolencia',
        'data',
        'nome',
        'idade',
        'faixaEtaria',
        'genero',
        'instrumentoViolencia',
        'numeroSei'
    ];

    /**
     * Relacionamento com Conflito
     */
    public function conflito()
    {
        return $this->belongsTo(Conflito::class);
    }
}