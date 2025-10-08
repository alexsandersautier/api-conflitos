<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoJudicial extends Model
{
    use HasFactory;

    protected $table = 'processo_judicial';
    protected $primaryKey = 'idProcessoJudicial';
    
    protected $fillable = ['idConflito', 
                           'data', 
                           'numero', 
                           'tipoPoder', 
                           'orgaoApoio', 
                           'numerosei'];
    
    protected $cast = ['data' => 'date'];

    
    public function conflito() {
        return $this->belongsTo(Conflito::class, 'idConflito');
    }
}
