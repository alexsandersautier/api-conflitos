<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoProcessoSei extends Model
{
    use HasFactory;

    protected $table = 'tipo_processo_sei';
    protected $primaryKey = 'idTipoProcessoSei';

    public $timestamps = false;
    
    protected $fillable = [
        'nome'
    ];

}
