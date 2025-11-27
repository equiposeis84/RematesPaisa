<?php

namespace App\Http\Controllers;

use App\Models\Pedidos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

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
        
        return view('pedidos')->with('datos', $datos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'idPedidos' => 'required|unique:pedidos,idPedidos',
            'fechaPedido' => 'required|date',
            'horaPedido' => 'required',
            'idCliente' => 'required|string|max:40',
            'valorPedido' => 'required|numeric|min:0',
            'ivaPedido' => 'required|numeric|min:0',
            'totalPedido' => 'required|numeric|min:0',
            'estadoPedido' => 'required|string|max:20',
            'repartidorPedido' => 'nullable|string|max:100'
        ],[
            'idPedidos.unique' => 'El ID del pedido ya existe en la base de datos.',
        ]);


        $cliente = DB::table('cliente')->where('idCliente', $request->idCliente)->first();
        
        if (!$cliente) {
           
            return redirect()->route('pedidos.index')
                ->with('error', 'El cliente con ID ' . $request->idCliente . ' no existe en la base de datos.')
                ->withInput();
        }

        //Agregar try-catch para manejar errores
        try {
            Pedidos::create($request->all());
            return redirect()->route('pedidos.index')->with('success', 'Pedido creado exitosamente');
            
        } catch (\Exception $e) {
            return redirect()->route('pedidos.index')
                ->with('error', 'Error al crear el pedido: ' . $e->getMessage())
                ->withInput();
        }
    }

    
    public function edit($idPedidos)
    {
        $pedido = Pedidos::findOrFail($idPedidos); 
        return view('pedidos.edit', compact('pedido')); 
    }

    public function update(Request $request, $idPedidos)
    {
        
        $cliente = DB::table('cliente')->where('idCliente', $request->idCliente)->first();
        
        if (!$cliente) {
            return redirect()->route('pedidos.index')
                ->with('error', 'El cliente con ID ' . $request->idCliente . ' no existe en la base de datos.')
                ->withInput();
        }

        $pedidos = Pedidos::findOrFail($idPedidos);
        
        $request->validate([
            'fechaPedido' => 'required|date',
            'horaPedido' => 'required',
            'idCliente' => 'required|string|max:40',
            'valorPedido' => 'required|numeric|min:0',
            'ivaPedido' => 'required|numeric|min:0',
            'totalPedido' => 'required|numeric|min:0',
            'estadoPedido' => 'required|string|max:20',
            'repartidorPedido' => 'nullable|string|max:100'
        ]);
        
        try {
            $pedidos->update([
                'fechaPedido' => $request->fechaPedido,
                'horaPedido' => $request->horaPedido,
                'idCliente' => $request->idCliente,
                'valorPedido' => $request->valorPedido,
                'ivaPedido' => $request->ivaPedido,
                'totalPedido' => $request->totalPedido,
                'estadoPedido' => $request->estadoPedido,
                'repartidorPedido' => $request->repartidorPedido
            ]);
            
            return redirect()->route('pedidos.index')->with('success', 'Pedido actualizado exitosamente');
            
        } catch (\Exception $e) {
            return redirect()->route('pedidos.index')
                ->with('error', 'Error al actualizar el pedido: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($idPedidos)
    {
        try {
            $pedidos = Pedidos::findOrFail($idPedidos);
            $pedidos->delete();
            return redirect()->route('pedidos.index')->with('success', 'Pedido eliminado exitosamente');
            
        } catch (\Exception $e) {
            return redirect()->route('pedidos.index')->with('error', 'Error al eliminar el pedido: ' . $e->getMessage());
        }
    }
}