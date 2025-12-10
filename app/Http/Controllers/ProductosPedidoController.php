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
        $productos = ProductosPedido::where('idPedido', $idPedido)->with('producto')->get();
        
        $productosDisponibles = Productos::all();
        
        return view('VistasAdmin.productos-pedido', compact('pedido', 'productos', 'productosDisponibles'));
    }

    public function show($idPedido, $idProducto)
    {
        $pedido = Pedidos::findOrFail($idPedido);
        $producto = ProductosPedido::where('idPedido', $idPedido)
            ->where('idProductos', $idProducto)
            ->with('producto')
            ->firstOrFail();
        
        return view('VistasAdmin.productos-pedido.show', compact('pedido', 'producto'));
    }

    public function create($idPedido)
    {
        $pedido = Pedidos::findOrFail($idPedido);
        $productosDisponibles = Productos::all();
        
        return view('VistasAdmin.productos-pedido.create', compact('pedido', 'productosDisponibles'));
    }

    public function edit($idPedido, $idProducto)
    {
        $pedido = Pedidos::findOrFail($idPedido);
        $producto = ProductosPedido::where('idPedido', $idPedido)
            ->where('idProductos', $idProducto)
            ->with('producto')
            ->firstOrFail();
        $productosDisponibles = Productos::all();
        
        return view('VistasAdmin.productos-pedido.edit', compact('pedido', 'producto', 'productosDisponibles'));
    }

    public function update(Request $request, $idPedido, $idProducto)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        $productoPedido = ProductosPedido::where('idPedido', $idPedido)
            ->where('idProductos', $idProducto)
            ->firstOrFail();

        $subtotal = $productoPedido->valorUnitarioDetalleProducto * $request->cantidad;
        $iva = $subtotal * 0.19;
        
        $productoPedido->update([
            'cantidadDetalleProducto' => $request->cantidad,
            'totalPagarDetalleProducto' => $subtotal,
            'ivaDetalleProducto' => $iva,
            'totalDetalleProducto' => $subtotal + $iva
        ]);

        $this->actualizarTotalPedido($idPedido);

        return redirect()->route('pedidos.productos.index', $idPedido)
            ->with('success', 'Producto actualizado exitosamente');
    }

    public function store(Request $request, $idPedido)
    {
        $request->validate([
            'idProducto' => 'required|exists:productos,idProductos',
            'cantidad' => 'required|integer|min:1'
        ]);

        $producto = Productos::findOrFail($request->idProducto);

        $productoExistente = ProductosPedido::where('idPedido', $idPedido)
            ->where('idProductos', $request->idProducto)
            ->first();

        if ($productoExistente) {
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

        $this->actualizarTotalPedido($idPedido);

        return redirect()->route('pedidos.productos.index', $idPedido)
            ->with('success', 'Producto agregado al pedido exitosamente');
    }

    public function destroy($idPedido, $idProducto)
    {
        $productoPedido = ProductosPedido::where('idPedido', $idPedido)
            ->where('idProductos', $idProducto)
            ->firstOrFail();

        $productoPedido->delete();

        $this->actualizarTotalPedido($idPedido);

        return redirect()->route('pedidos.productos.index', $idPedido)
            ->with('success', 'Producto eliminado del pedido exitosamente');
    }

    private function actualizarTotalPedido($idPedido)
    {
        $totalProductos = ProductosPedido::where('idPedido', $idPedido)->sum('totalPagarDetalleProducto');
        
        $pedido = Pedidos::findOrFail($idPedido);
        $iva = $totalProductos * 0.19;
        $total = $totalProductos + $iva;

        $pedido->update([
            'valorPedido' => $totalProductos,
            'ivaPedido' => $iva,
            'totalPedido' => $total
        ]);
    }
}