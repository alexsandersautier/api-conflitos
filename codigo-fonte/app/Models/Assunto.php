<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assunto extends Model
{
    use HasFactory;

    protected $table = 'assunto';
    protected $primaryKey = 'idAssunto';
    
    protected $fillable = [
        'nome'
    ];

}
