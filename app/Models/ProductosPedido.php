<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductosPedido extends Model
{
    use HasFactory;

    protected $table = 'productos_pedidos';
    
    protected $fillable = [
        'idPedido',
        'idProducto',
        'nombreProducto',
        'precio',
        'cantidad',
        'subtotal'
    ];

    public $timestamps = true;

    // Relación con el pedido
    public function pedido()
    {
        return $this->belongsTo(Pedidos::class, 'idPedido', 'idPedidos');
    }
}