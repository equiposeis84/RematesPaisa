<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';
    
    protected $primaryKey = 'idProveedores';
    
    protected $fillable = [
        'idProveedores',
        'tipoDocumentoProveedor',
        'nombreProveedor',
        'telefonoProveedor',
        'correoProveedor',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}