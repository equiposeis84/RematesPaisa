<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleProductos extends Model
{
    protected $table = 'detalleproductos';
    protected $primaryKey = 'idDetalleProducto'; // O el nombre correcto de tu primary key
    public $timestamps = true; // Cambia a true si tienes timestamps
    
    protected $fillable = [
        'idPedido',
        'idProductos',
        'cantidadDetalleProducto',
        'valorUnitarioDetalleProducto',
        'totalPagarDetalleProducto',
        'ivaDetalleProducto',
        'totalDetalleProducto'
    ];

    public function producto()
    {
        return $this->belongsTo(Productos::class, 'idProductos', 'idProductos');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedidos::class, 'idPedido', 'idPedidos');
    }
}