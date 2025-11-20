<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedidos extends Model
{
    use HasFactory;

    protected $table = 'pedidos'; 
    
    protected $primaryKey = 'idPedidos';
    
    public $incrementing = true;
    
    protected $keyType = 'integer';
    
    protected $fillable = [
        'idPedidos',
        'fechaPedido',
        'horaPedido',
        'idCliente',
        'valorPedido',
        'ivaPedido',
        'totalPedido',
        'estadoPedido',
        'repartidorPedido'
    ];

    public $timestamps = false;
}