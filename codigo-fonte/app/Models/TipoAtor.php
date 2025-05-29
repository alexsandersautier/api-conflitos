<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAtor extends Model
{
    use HasFactory;

    protected $table = 'tipo_ator';
    protected $primaryKey = 'idTipoAtor';

    public $timestamps = false;
    
    protected $fillable = [
        'nome'
    ];

}
