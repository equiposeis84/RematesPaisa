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
        'NombreEmpresa',
        'idCliente',
        'tipoDocumentoCliente',
        'nombreCliente',
        'apellidoCliente',
        'direccionCliente',
        'telefonoCliente',
        'emailCliente'
    ];

    public $timestamps = false;
}