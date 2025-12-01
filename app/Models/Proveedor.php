<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

<<<<<<< HEAD
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
=======
    protected $table = 'proveedores'; 
    
    protected $primaryKey = 'NITProveedores';
    
    public $incrementing = false;
    
    protected $keyType = 'int';
    
    protected $fillable = [
        'NITProveedores',
        'nombreProveedor',
        'telefonoProveedor',
        'correoProveedor'
    ];

    public $timestamps = false;
<<<<<<< HEAD
>>>>>>> 1992225baf11169504a8d35174321996067799e9
=======

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Productos::class, 'NITProveedores', 'NITProveedores');
    }
>>>>>>> 516688caa403d940564b5ec3d69001bde4adad27
}