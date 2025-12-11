<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaAtor extends Model
{
    use HasFactory;

    protected $table = 'categoria_ator';

    protected $primaryKey = 'idCategoriaAtor';

    public $timestamps = false;

    protected $fillable = [
        'nome'
    ];
}
