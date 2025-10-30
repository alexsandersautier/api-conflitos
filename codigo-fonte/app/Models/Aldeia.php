<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aldeia extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'aldeia';
    protected $primaryKey = 'idAldeia';
    
    protected $fillable = [
                'nm_uf',
                'nm_munic',
                'nome',
                'situacao',
                'fase',
                'amz_leg',
                'lat',
    ];
        
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    // Escopo para aldeias ativas
    public function scopeAtiva($query)
    {
        return $query->where('fase', 'Regularizada');
    }

}