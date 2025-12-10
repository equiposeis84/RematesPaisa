<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosPedido extends Model
{
    use HasFactory;

    protected $table = 'detalleproductos';
    
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

    public function pedido()
    {
        return $this->belongsTo(Pedidos::class, 'idPedido', 'idPedidos');
    }

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'idProductos', 'idProductos');
    }

    public function getUniqueKeyAttribute()
    {
        return $this->idPedido . '_' . $this->idProductos;
    }
}