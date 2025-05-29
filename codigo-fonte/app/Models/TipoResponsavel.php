<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoResponsavel extends Model
{
    use HasFactory;

    protected $table = 'tipo_responsavel';
    protected $primaryKey = 'idTipoResponsavel';

    public $timestamps = false;
    
    protected $fillable = [
        'nome'
    ];

}
