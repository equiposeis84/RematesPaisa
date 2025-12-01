<?php

namespace App\Http\Controllers;

use App\Models\Pedidos;
use App\Models\ProductosPedido;
use App\Models\Productos;
use Illuminate\Http\Request;

class ProductosPedidoController extends Controller
{
    public function index($idPedido)
    {
        $pedido = Pedidos::findOrFail($idPedido);
        $productos = ProductosPedido::where('idPedido', $idPedido)->get();
        
        // Obtener productos disponibles de la base de datos
        $productosDisponibles = Productos::all();
        
        return view('productos-pedido', compact('pedido', 'productos', 'productosDisponibles'));
    }

    public function store(Request $request, $idPedido)
    {
        $request->validate([
            'idProducto' => 'required|exists:productos,idProductos',
            'cantidad' => 'required|integer|min:1'
        ]);

        // Obtener el producto seleccionado
        $producto = Productos::findOrFail($request->idProducto);

        // Verificar si el producto ya está en el pedido
        $productoExistente = ProductosPedido::where('idPedido', $idPedido)
            ->where('idProductos', $request->idProducto)
            ->first();

        if ($productoExistente) {
            // Actualizar cantidad si el producto ya existe
            $nuevaCantidad = $productoExistente->cantidadDetalleProducto + $request->cantidad;
            $subtotal = $nuevaCantidad * $productoExistente->valorUnitarioDetalleProducto;
            $iva = $subtotal * 0.19;
            
            $productoExistente->update([
                'cantidadDetalleProducto' => $nuevaCantidad,
                'totalPagarDetalleProducto' => $subtotal,
                'ivaDetalleProducto' => $iva,
                'totalDetalleProducto' => $subtotal + $iva
            ]);
        } else {
            // Crear nuevo producto en el pedido
            $subtotal = $producto->precioUnitario * $request->cantidad;
            $iva = $subtotal * 0.19;
            
            ProductosPedido::create([
                'idPedido' => $idPedido,
                'idProductos' => $producto->idProductos,
                'cantidadDetalleProducto' => $request->cantidad,
                'valorUnitarioDetalleProducto' => $producto->precioUnitario,
                'totalPagarDetalleProducto' => $subtotal,
                'ivaDetalleProducto' => $iva,
                'totalDetalleProducto' => $subtotal + $iva
            ]);
        }

        // Actualizar el total del pedido
        $this->actualizarTotalPedido($idPedido);

        return redirect()->route('pedidos.productos.index', $idPedido)
            ->with('success', 'Producto agregado al pedido exitosamente');
    }

    public function destroy($idPedido, $idProducto)
    {
        // Buscar el producto por idPedido e idProductos
        $productoPedido = ProductosPedido::where('idPedido', $idPedido)
            ->where('idProductos', $idProducto)
            ->firstOrFail();

        $productoPedido->delete();

        // Actualizar el total del pedido
        $this->actualizarTotalPedido($idPedido);

        return redirect()->route('pedidos.productos.index', $idPedido)
            ->with('success', 'Producto eliminado del pedido exitosamente');
    }

    private function actualizarTotalPedido($idPedido)
    {
        $totalProductos = ProductosPedido::where('idPedido', $idPedido)->sum('totalPagarDetalleProducto');
        
        $pedido = Pedidos::findOrFail($idPedido);
        $iva = $totalProductos * 0.19; // 19% de IVA
        $total = $totalProductos + $iva;

        $pedido->update([
            'valorPedido' => $totalProductos,
            'ivaPedido' => $iva,
            'totalPedido' => $total
        ]);
    }
}