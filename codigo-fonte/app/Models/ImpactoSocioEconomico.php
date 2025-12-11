<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpactoSocioEconomico extends Model
{
    use HasFactory;

    protected $table = 'impacto_socio_economico';

    protected $primaryKey = 'idImpactoSocioEconomico';

    public $timestamps = false;

    protected $fillable = [
        'nome'
    ];
}
