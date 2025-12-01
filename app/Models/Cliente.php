<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Cliente ahora es usuario

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
        'idRol'
    ];

    public $timestamps = false;

    // Relación con roles para obtener el nombre del rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idRol', 'idRol');
    }

    // Accessor para obtener el nombre del rol
    public function getNombreRolAttribute()
    {
        return $this->rol ? $this->rol->nombreRol : 'Sin Rol';
    }
}