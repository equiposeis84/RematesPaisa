<?php

namespace App\Http\Controllers;

use App\Models\Pedidos;
use App\Models\ProductosPedido;
use Illuminate\Http\Request;

class ProductosPedidoController extends Controller
{
    public function index($idPedido)
    {
        $pedido = Pedidos::findOrFail($idPedido);
        $productos = ProductosPedido::where('idPedido', $idPedido)->get();
        
        // Aquí deberías obtener los productos disponibles de tu base de datos
        $productosDisponibles = []; // Esto lo cambias según tu estructura de productos
        
        return view('productos-pedido', compact('pedido', 'productos', 'productosDisponibles'));
    }

    public function store(Request $request, $idPedido)
    {
        $request->validate([
            'idProducto' => 'required',
            'nombreProducto' => 'required',
            'precio' => 'required|numeric|min:0',
            'cantidad' => 'required|integer|min:1'
        ]);

        $subtotal = $request->precio * $request->cantidad;

        ProductosPedido::create([
            'idPedido' => $idPedido,
            'idProducto' => $request->idProducto,
            'nombreProducto' => $request->nombreProducto,
            'precio' => $request->precio,
            'cantidad' => $request->cantidad,
            'subtotal' => $subtotal
        ]);

        // Actualizar el total del pedido
        $this->actualizarTotalPedido($idPedido);

        return redirect()->route('pedidos.productos.index', $idPedido)
            ->with('success', 'Producto agregado al pedido exitosamente');
    }

    public function destroy($idPedido, $idProductoPedido)
    {
        $productoPedido = ProductosPedido::findOrFail($idProductoPedido);
        $productoPedido->delete();

        // Actualizar el total del pedido
        $this->actualizarTotalPedido($idPedido);

        return redirect()->route('pedidos.productos.index', $idPedido)
            ->with('success', 'Producto eliminado del pedido exitosamente');
    }

    private function actualizarTotalPedido($idPedido)
    {
        $totalProductos = ProductosPedido::where('idPedido', $idPedido)->sum('subtotal');
        
        $pedido = Pedidos::findOrFail($idPedido);
        $pedido->update([
            'valorPedido' => $totalProductos,
            'ivaPedido' => $totalProductos * 0.19, // 19% de IVA, ajusta según necesites
            'totalPedido' => $totalProductos * 1.19
        ]);
    }
}