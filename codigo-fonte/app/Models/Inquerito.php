<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquerito extends Model
{
    use HasFactory;

    protected $table = 'inquerito';

    protected $primaryKey = 'idInquerito';

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
