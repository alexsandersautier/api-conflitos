<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ator extends Model
{
    use HasFactory;

    protected $table = 'ator';
    protected $primaryKey = 'idAtor';

    public $timestamps = false;
    
    protected $fillable = [
        'nome'
    ];

}
