<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramaProtecao extends Model
{
    use HasFactory;

    protected $table = 'programa_protecao';

    protected $primaryKey = 'idProgramaProtecao';

    protected $fillable = [
        'idConflito',
        'tipoPrograma',
        'uf',
        'numeroSei'
    ];

    public function conflito()
    {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
}
