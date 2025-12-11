<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroBoNf extends Model
{
    use HasFactory;

    protected $table = 'registro_bo_nf';

    protected $primaryKey = 'idRegistroBoNf';

    protected $fillable = [
        'idConflito',
        'data',
        'numero',
        'orgao',
        'tipoOrgao',
        'numeroSei'
    ];

    public function conflito()
    {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
}
