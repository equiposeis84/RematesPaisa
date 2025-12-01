<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use Illuminate\Http\Request;

class PedidosController extends Controller
{
    //
}
=======
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
        
        $datos = $query->orderBy('idPedidos', 'desc')->paginate(10);
        
        return view('pedidos')->with('datos', $datos);
    }

    public function create()
    {
        $clientes = Cliente::all();
        $repartidores = Usuario::where('idRol', 3)->get(); // Rol 3 para repartidores
        
        return view('pedidos.create', compact('clientes', 'repartidores'));
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
        $clientes = Cliente::all();
        $repartidores = Usuario::where('idRol', 3)->get();
        
        return view('pedidos.edit', compact('pedido', 'clientes', 'repartidores'));
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
<<<<<<< HEAD
}
>>>>>>> 1992225baf11169504a8d35174321996067799e9
=======

    // Método para obtener información del cliente (para AJAX)
    public function getClienteInfo($idCliente)
    {
        $cliente = Cliente::find($idCliente);
        
        if ($cliente) {
            return response()->json([
                'success' => true,
                'cliente' => [
                    'nombre' => $cliente->nombreCliente,
                    'apellido' => $cliente->apellidoCliente,
                    'empresa' => $cliente->NombreEmpresa,
                    'email' => $cliente->emailCliente,
                    'telefono' => $cliente->telefonoCliente,
                    'direccion' => $cliente->direccionCliente
                ]
            ]);
        }
        
        return response()->json(['success' => false]);
    }
}
>>>>>>> 516688caa403d940564b5ec3d69001bde4adad27
