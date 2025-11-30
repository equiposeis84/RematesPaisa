<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosPedido extends Model
{
    use HasFactory;

    protected $table = 'detalleproductos';
    
    // Como es una tabla con clave primaria compuesta, desactivamos la clave primaria automática
    protected $primaryKey = null;
    public $incrementing = false;
    
    protected $fillable = [
        'idPedido',
        'idProductos',
        'cantidadDetalleProducto',
        'valorUnitarioDetalleProducto',
        'totalPagarDetalleProducto',
        'ivaDetalleProducto',
        'totalDetalleProducto'
    ];

    public $timestamps = false;

    // Relación con el pedido
    public function pedido()
    {
        return $this->belongsTo(Pedidos::class, 'idPedido', 'idPedidos');
    }

    // Relación con el producto
    public function producto()
    {
        return $this->belongsTo(Productos::class, 'idProductos', 'idProductos');
    }

    // Método para obtener un identificador único para este registro
    public function getUniqueKeyAttribute()
    {
        return $this->idPedido . '_' . $this->idProductos;
    }
}