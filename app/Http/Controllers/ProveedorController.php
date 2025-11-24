<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $datos = Proveedor::when($search, function($query, $search) {
            return $query->where('nombreProveedor', 'LIKE', "%{$search}%")
                        ->orWhere('idProveedores', 'LIKE', "%{$search}%")
                        ->orWhere('correoProveedor', 'LIKE', "%{$search}%");
        })->paginate(10);

        return view('proveedores', compact('datos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idProveedor' => 'required|unique:proveedores,idProveedores',
            'tipoDocumentoProveedor' => 'required',
            'nombreProveedor' => 'required',
            'emailProveedor' => 'required|email',
        ]);

        try {
            Proveedor::create([
                'idProveedores' => $request->idProveedor,
                'tipoDocumentoProveedor' => $request->tipoDocumentoProveedor,
                'nombreProveedor' => $request->nombreProveedor,
                'telefonoProveedor' => $request->telefonoProveedor,
                'correoProveedor' => $request->emailProveedor,
                'direccionProveedor' => $request->direccionProveedor,
            ]);

            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor creado exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')
                ->with('error', 'Error al crear el proveedor: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipoDocumentoProveedor' => 'required',
            'nombreProveedor' => 'required',
            'emailProveedor' => 'required|email',
        ]);

        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->update([
                'tipoDocumentoProveedor' => $request->tipoDocumentoProveedor,
                'nombreProveedor' => $request->nombreProveedor,
                'telefonoProveedor' => $request->telefonoProveedor,
                'correoProveedor' => $request->emailProveedor,
                'direccionProveedor' => $request->direccionProveedor,
            ]);

            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor actualizado exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')
                ->with('error', 'Error al actualizar el proveedor: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $proveedor = Proveedor::findOrFail($id);
            $proveedor->delete();

            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor eliminado exitosamente.');
                
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')
                ->with('error', 'Error al eliminar el proveedor: ' . $e->getMessage());
        }
    }
}