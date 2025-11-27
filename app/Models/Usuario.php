<?php

namespace App\Models;

<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> 1992225baf11169504a8d35174321996067799e9
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
<<<<<<< HEAD
    protected $table = 'usuarios';
    protected $primaryKey = 'nombreUsuario';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'nombreUsuario',
        'passwordUsuario',
        'idRoles'
    ];
}
=======
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
>>>>>>> 1992225baf11169504a8d35174321996067799e9
