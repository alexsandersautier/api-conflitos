<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtorIdentificadoConflito extends Model
{

    protected $table = 'ator_identificado_conflito';

    protected $primaryKey = 'idAtorIdentificadoConflito';

    protected $fillable = [
        'idConflito',
        'nome'
    ];

    public $timestamps = false;

    public function conflito()
    {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
}
