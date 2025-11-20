<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;

    protected $table = 'productos'; 
    
    protected $primaryKey = 'idProductos';
    
    public $incrementing = true;
    
    protected $keyType = 'integer';
    
    protected $fillable = [
        'idProductos',
        'nombreProducto',
        'entradaProducto',
        'salidaProducto',
        'categoriaProducto',
        'idProveedores',
        'precioUnitario'
    ];

    public $timestamps = false;
}