<?php

namespace App\Http\Controllers;

use App\Models\Pedidos;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Http\Request;

class PedidosController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = Pedidos::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('idPedidos', 'LIKE', "%{$search}%")
                ->orWhere('idCliente', 'LIKE', "%{$search}%")
                ->orWhere('estadoPedido', 'LIKE', "%{$search}%")
                ->orWhere('repartidorPedido', 'LIKE', "%{$search}%");
            });
        }
        
        $datos = $query->orderBy('idPedidos', 'asc')->paginate(10);
        
        // Obtener el próximo ID para el modal
        $lastPedido = Pedidos::orderBy('idPedidos', 'desc')->first();
        $nextId = $lastPedido ? $lastPedido->idPedidos + 1 : 1;
        
        return view('pedidos')->with([
            'datos' => $datos,
            'nextId' => $nextId
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'idPedidos' => 'required|unique:pedidos,idPedidos',
            'fechaPedido' => 'required|date',
            'horaPedido' => 'required',
            'idCliente' => 'required|exists:cliente,idCliente',
            'valorPedido' => 'required|numeric|min:0',
            'estadoPedido' => 'required|string|max:20',
            'repartidorPedido' => 'nullable|string|max:100'
        ]);

        // Calcular IVA (19%) y total automáticamente
        $iva = $request->valorPedido * 0.19;
        $total = $request->valorPedido + $iva;

        Pedidos::create([
            'idPedidos' => $request->idPedidos,
            'fechaPedido' => $request->fechaPedido,
            'horaPedido' => $request->horaPedido,
            'idCliente' => $request->idCliente,
            'valorPedido' => $request->valorPedido,
            'ivaPedido' => $iva,
            'totalPedido' => $total,
            'estadoPedido' => $request->estadoPedido,
            'repartidorPedido' => $request->repartidorPedido
        ]);

        return redirect()->route('pedidos.index')
                        ->with('success', 'Pedido creado exitosamente');
    }

    public function edit($idPedidos)
    {
        $pedido = Pedidos::findOrFail($idPedidos);
        return view('pedidos.edit', compact('pedido'));
    }

    public function update(Request $request, $idPedidos)
    {
        $pedido = Pedidos::findOrFail($idPedidos);
        
        $request->validate([
            'fechaPedido' => 'required|date',
            'horaPedido' => 'required',
            'idCliente' => 'required|exists:cliente,idCliente',
            'valorPedido' => 'required|numeric|min:0',
            'estadoPedido' => 'required|string|max:20',
            'repartidorPedido' => 'nullable|string|max:100'
        ]);

        // Calcular IVA (19%) y total automáticamente
        $iva = $request->valorPedido * 0.19;
        $total = $request->valorPedido + $iva;
        
        $pedido->update([
            'fechaPedido' => $request->fechaPedido,
            'horaPedido' => $request->horaPedido,
            'idCliente' => $request->idCliente,
            'valorPedido' => $request->valorPedido,
            'ivaPedido' => $iva,
            'totalPedido' => $total,
            'estadoPedido' => $request->estadoPedido,
            'repartidorPedido' => $request->repartidorPedido
        ]);
        
        return redirect()->route('pedidos.index')
                        ->with('success', 'Pedido actualizado exitosamente');
    }

    public function destroy($idPedidos)
    {
        try {
            $pedido = Pedidos::findOrFail($idPedidos);
            $pedido->delete();
            
            return redirect()->route('pedidos.index')
                ->with('success', 'Pedido eliminado correctamente');
                
        } catch (\Exception $e) {
            return redirect()->route('pedidos.index')
                ->with('error', 'Error al eliminar el pedido: ' . $e->getMessage());
        }
    }
}