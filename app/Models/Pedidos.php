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
        'documento',
        'valorPedido',
        'ivaPedido',
        'totalPedido',
        'estadoPedido',
        'repartidorPedido'
    ];

    public $timestamps = false;

    // Relación con usuario (cliente)
    public function cliente()
    {
        return $this->belongsTo(Usuario::class, 'documento', 'documento');
    }

    // Relación con productos del pedido
    public function productos()
    {
        return $this->hasMany(ProductosPedido::class, 'idPedido', 'idPedidos');
    }
}