<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalidadeConflito extends Model
{
    use HasFactory;

    protected $table = 'localidade_conflito';

    protected $primaryKey = 'idLocalidadeConflito';

    protected $fillable = [
        'idConflito',
        'regiao',
        'uf',
        'municipio'
    ];

    public function conflito()
    {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
}
