<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SituacaoFundiaria extends Model
{
    use HasFactory;

    protected $table = 'situacao_fundiaria';

    protected $primaryKey = 'idSituacaoFundiaria';

    public $timestamps = false;

    protected $fillable = [
        'nome'
    ];
}
