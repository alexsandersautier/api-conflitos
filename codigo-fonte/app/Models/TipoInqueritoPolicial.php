<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInqueritoPolicial extends Model
{
    use HasFactory;

    protected $table = 'tipo_inquerito_policial';
    protected $primaryKey = 'idTipoInqueritoPolicial';

    public $timestamps = false;
    
    protected $fillable = [
        'nome'
    ];

}
