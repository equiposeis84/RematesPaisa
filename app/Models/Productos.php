<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

<<<<<<< HEAD
class Productos extends Model  // ← CAMBIAR de 'Producto' a 'Productos'
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'idProductos';
    
    // Y también CORREGIR los nombres de campos según tu base de datos:
    protected $fillable = [
        'idProductos',
        'nombreProducto',      // ← sin 's' (como en tu BD)
        'entradaProducto',     // ← sin 's'
        'salidaProducto',      // ← sin 's'
        'categoriaProducto',   // ← sin 's'
        'idProveedores',
        'precioUnitario'       // ← "precio" no "predo"
    ];

    protected $casts = [
        'precioUnitario' => 'decimal:2',  // ← corregido
        'entradaProducto' => 'integer',   // ← sin 's'
        'salidaProducto' => 'integer',    // ← sin 's'
        'idProveedores' => 'integer'
    ];

    public $timestamps = false;

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('nombreProducto', 'LIKE', "%{$search}%")  // ← sin 's'
                        ->orWhere('categoriaProducto', 'LIKE', "%{$search}%");  // ← sin 's'
        }
        return $query;
    }
=======
class Productos extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'idProductos';
    
    protected $fillable = [
        'nombreProducto',
        'entradaProducto',
        'salidaProducto',
        'categoriaProducto',
        'NITProveedores',
        'precioUnitario'
    ];

    public $timestamps = false;
<<<<<<< HEAD
>>>>>>> 1992225baf11169504a8d35174321996067799e9
=======

    // Relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'NITProveedores', 'NITProveedores');
    }
>>>>>>> 516688caa403d940564b5ec3d69001bde4adad27
}