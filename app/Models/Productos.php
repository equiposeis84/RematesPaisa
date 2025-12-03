<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'idProductos';
    
    protected $fillable = [
        'idProductos',
        'nombreProducto',
        'entradaProducto',
        'salidaProducto',
        'categoriaProducto',
        'NITProveedores',
        'precioUnitario'
    ];

    public $timestamps = false;

    // Relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'NITProveedores', 'NITProveedores');
    }
}