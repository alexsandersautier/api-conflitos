<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolenciaPatrimonial extends Model
{
    use HasFactory;

    protected $table      = 'violencia_patrimonial';
    protected $primaryKey = 'idViolenciaPatrimonial';
    
    protected $fillable = ['idConflito',
                            'tipoViolencia',
                            'data',
                            'numeroSei'];

    protected $casts = [
        'data' => 'date',
    ];

    /**
     * Relacionamento com Conflito
     */
    public function conflito()
    {
        return $this->belongsTo(Conflito::class);
    }
}