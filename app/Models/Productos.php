<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}