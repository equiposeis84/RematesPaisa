// app/Models/Usuario.php - CORRECCIÓN
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'idUsuario'; // CAMBIAR DE 'id' a 'idUsuario'

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'idRol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Si necesitas la relación con cliente
    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'idUsuario', 'idUsuario');
    }
}