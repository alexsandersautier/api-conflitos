<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Povo extends Model
{
    use HasFactory;

    protected $table = 'povo';
    protected $primaryKey = 'idPovo';

    protected $fillable = [
        'nome',
        'codEtnia',
        'lingua',
        'familia_linguistica',
        'ufs_povo',
        'qtd_ti_povo'
    ];


}
