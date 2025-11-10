<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = Cliente::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombreCliente', 'LIKE', "%{$search}%")
                  ->orWhere('apellidoCliente', 'LIKE', "%{$search}%")
                  ->orWhere('emailCliente', 'LIKE', "%{$search}%")
                  ->orWhere('tipoDocumentoCliente', 'LIKE', "%{$search}%");
            });
        }
        
        $datos = $query->orderBy('idCliente', 'desc')->paginate(10);
        
        return view('clientes', compact('datos', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // No se usa porque estamos usando modal
        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'idCliente' => 'required|string|max:40|unique:cliente,idCliente',
            'tipoDocumentoCliente' => 'required|string|max:20',
            'nombreCliente' => 'required|string|max:100',
            'apellidoCliente' => 'required|string|max:100',
            'emailCliente' => 'required|email|unique:cliente,emailCliente', 
            'telefonoCliente' => 'nullable|string|max:20',
            'direccionCliente' => 'nullable|string|max:255'
        ]);

        Cliente::create($request->all());

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        // No se usa
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        // No se usa porque estamos usando modal
        return abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'tipoDocumentoCliente' => 'required|string|max:20',
            'nombreCliente' => 'required|string|max:100',
            'apellidoCliente' => 'required|string|max:100',
            'emailCliente' => 'required|email|unique:cliente,emailCliente,' . $cliente->idCliente . ',idCliente',
            'telefonoCliente' => 'nullable|string|max:20',
            'direccionCliente' => 'nullable|string|max:255'
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente actualizado exitosamente.');
    }

        public function destroy($idCliente)
    {
        // Buscar el cliente manualmente por su idCliente
        $cliente = Cliente::where('idCliente', $idCliente)->first();
        
        // Verificar si el cliente existe
        if (!$cliente) {
            return redirect()->route('clientes.index')
                            ->with('error', 'Cliente no encontrado');
        }
        
        // Verificar si el cliente tiene pedidos
        $tienePedidos = \DB::table('pedidos')->where('idCliente', $idCliente)->exists();
        
        if ($tienePedidos) {
            return redirect()->route('clientes.index')
                            ->with('error', 'No se puede eliminar el cliente porque tiene pedidos asociados');
        }
        
        // Eliminar el cliente
        $cliente->delete();

        return redirect()->route('clientes.index')
                        ->with('success', 'Cliente eliminado exitosamente.');
    }
}