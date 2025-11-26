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
    public $timestamps = false;

    protected $fillable = [

        'idCliente',
        'NombreEmpresa',
        'tipoDocumentoCliente',
        'nombreCliente',
        'apellidoCliente',
        'emailCliente',
        'telefonoCliente',
        'direccionCliente'
    ];
}
