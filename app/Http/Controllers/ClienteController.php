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
                  ->orWhere('idCliente', 'LIKE', "%{$search}%")
                  ->orWhere('NombreEmpresa', 'LIKE', "%{$search}%")
                  ->orWhere('tipoDocumentoCliente', 'LIKE', "%{$search}%");
            });
        }
        
        $datos = $query->orderBy('idCliente', 'desc')->paginate(10);
        
        return view('clientes')->with('datos', $datos);
    }

    public function store(Request $request){
        $request->validate([
            'NombreEmpresa' => 'required|string|max:100',
            'idCliente' => 'required|unique:cliente,idCliente',
            'tipoDocumentoCliente' => 'required|string|max:45',
            'nombreCliente' => 'required|string|max:45',
            'apellidoCliente' => 'required|string|max:45',
            'direccionCliente' => 'required|string|max:45',
            'telefonoCliente' => 'required|string|max:45',
            'emailCliente' => 'required|email|unique:cliente,emailCliente|max:45'
        ],[
             'idCliente.unique' => 'El ID del cliente ya existe en la base de datos.',
             'emailCliente.unique' => 'El email ya está en uso por otro cliente.',
        ]);

        Cliente::create($request->all());
        return redirect()->route('clientes.index')->with('success', 'Cliente creado exitosamente');
    }

    public function edit($idCliente)
    {
        $cliente = Cliente::findOrFail($idCliente);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $idCliente){
        $cliente = Cliente::findOrFail($idCliente);
        
        $request->validate([
            'NombreEmpresa' => 'required|string|max:100',
            'tipoDocumentoCliente' => 'required|string|max:45',
            'nombreCliente' => 'required|string|max:45',
            'apellidoCliente' => 'required|string|max:45',
            'direccionCliente' => 'required|string|max:45',
            'telefonoCliente' => 'required|string|max:45',
            'emailCliente' => 'required|email|unique:cliente,emailCliente,'.$idCliente.',idCliente|max:45'
        ],[
            'emailCliente.unique' => 'El email ya está en uso por otro cliente.',
        ]);
        
        $cliente->update([
            'NombreEmpresa' => $request->NombreEmpresa,
            'tipoDocumentoCliente' => $request->tipoDocumentoCliente,
            'nombreCliente' => $request->nombreCliente,
            'apellidoCliente' => $request->apellidoCliente,
            'direccionCliente' => $request->direccionCliente,
            'telefonoCliente' => $request->telefonoCliente,
            'emailCliente' => $request->emailCliente
        ]);
        
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy($idCliente){
        try{
            $cliente = Cliente::findOrFail($idCliente);
            
            // Verificar si el cliente tiene pedidos asociados
            $tienePedidos = \DB::table('pedidos')->where('idCliente', $idCliente)->exists();
            
            if ($tienePedidos) {
                return redirect()->route('clientes.index')->with('error', 'No se puede eliminar el cliente porque tiene pedidos asociados');
            }
            
            $cliente->delete();
            return redirect()->route('clientes.index')->with('success', 'Cliente eliminado exitosamente');
            
        }catch(\Exception $e){
            return redirect()->route('clientes.index')->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}