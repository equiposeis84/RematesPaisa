<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'cliente';
    protected $primaryKey = 'idCliente';
<<<<<<< HEAD
    public $incrementing = false; // Como es documento, NO debe autoincrementar
    protected $keyType = 'string'; // Documento puede contener letras o ceros a la izquierda
<<<<<<< HEAD
    public $timestamps = false;

    protected $fillable = [

=======
   

    protected $fillable = [
        'NitEmpresa',
>>>>>>> 1992225baf11169504a8d35174321996067799e9
=======
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
>>>>>>> 516688caa403d940564b5ec3d69001bde4adad27
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
    public $timestamps = false;
>>>>>>> 1992225baf11169504a8d35174321996067799e9
}
=======

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
>>>>>>> 516688caa403d940564b5ec3d69001bde4adad27
