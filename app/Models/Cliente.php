<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'cliente';
    protected $primaryKey = 'idCliente';
    public $incrementing = false; // Como es documento, NO debe autoincrementar
    protected $keyType = 'string'; // Documento puede contener letras o ceros a la izquierda
<<<<<<< HEAD
    public $timestamps = false;

    protected $fillable = [

=======
   

    protected $fillable = [
        'NitEmpresa',
>>>>>>> 1992225baf11169504a8d35174321996067799e9
        'idCliente',
        'NombreEmpresa',
        'tipoDocumentoCliente',
        'nombreCliente',
        'apellidoCliente',
        'emailCliente',
        'telefonoCliente',
        'direccionCliente'
    ];
<<<<<<< HEAD
=======
    public $timestamps = false;
>>>>>>> 1992225baf11169504a8d35174321996067799e9
}
