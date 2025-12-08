<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'cliente';
    protected $primaryKey = 'idCliente';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idCliente',
        'NombreEmpresa',
        'tipoDocumentoCliente',
        'nombreCliente',
        'apellidoCliente',
        'emailCliente',
        'telefonoCliente',
        'direccionCliente',
        'password',
        'idRol',
        'idUsuario'
    ];

    public $timestamps = false;

    protected $hidden = ['password'];

    // Métodos para autenticación (si vas a usar Cliente para login)
    public function getAuthIdentifierName()
    {
        return 'idCliente';
    }
    
    public function getAuthPassword()
    {
        return $this->password;
    }
    
    public function getEmailForPasswordReset()
    {
        return $this->emailCliente;
    }

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }

    // Relación con roles
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idRol', 'idRol');
    }
}