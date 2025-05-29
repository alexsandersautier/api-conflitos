<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasFactory;

    protected $table = 'perfil';
    protected $primaryKey = 'idPerfil';

    public $timestamps = false;
    
    protected $fillable = [
        'nome'
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idPerfil');
    }

}
