<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolenciaPessoaNaoIndigena extends Model
{
    use HasFactory;

    protected $table = 'violencia_pessoa_nao_indigena';

    protected $primary = 'idViolenciaPessoaNaoIndigena';

    protected $fillable = [
        'idConflito',
        'tipoViolencia',
        'tipoPessoa',
        'data',
        'nome',
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