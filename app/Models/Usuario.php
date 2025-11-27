<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'idUsuario';
    
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'idRol'
    ];

    protected $hidden = [
        'password'
    ];

    // Relación con el rol
    public function rol()
    {
        return $this->belongsTo(Roles::class, 'idRol', 'idRol');
    }

    // Métodos helper para verificar roles
    public function esAdmin()
    {
        return $this->idRol == 1;
    }

    public function esCliente()
    {
        return $this->idRol == 2;
    }

    public function esRepartidor()
    {
        return $this->idRol == 3;
    }
}