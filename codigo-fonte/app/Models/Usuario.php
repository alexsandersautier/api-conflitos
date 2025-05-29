<?php
// app/Models/Usuario.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'usuario';
    protected $primaryKey = 'idUsuario';

    protected $fillable = [
        'idOrgao',
        'idPerfil',
        'nome',
        'email',
        'senha',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function orgao()
    {
        return $this->belongsTo(Orgao::class, 'idOrgao');
    }
    
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'idPerfil');
    }
}
