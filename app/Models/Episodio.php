<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episodio extends Model
{
    use HasFactory;

    protected $table = 'episodio';
    protected $primaryKey = 'idEpisodio';

    protected $fillable = [
        'idConflito',
        'titulo',
        'descricao',
        'dataHora'
    ];
    
    public $timestamps = false;

    public function conflito() {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
}
