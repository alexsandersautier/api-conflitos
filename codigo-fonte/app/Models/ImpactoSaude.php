<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpactoSaude extends Model
{
    use HasFactory;

    protected $table = 'impacto_saude';

    protected $primaryKey = 'idImpactoSaude';

    public $timestamps = false;

    protected $fillable = [
        'nome'
    ];
}
