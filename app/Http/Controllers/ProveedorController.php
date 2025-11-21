<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Models\Productos;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = Proveedor::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('NITProveedores', 'LIKE', "%{$search}%")
                  ->orWhere('nombreProveedor', 'LIKE', "%{$search}%")
                  ->orWhere('correoProveedor', 'LIKE', "%{$search}%")
                  ->orWhere('telefonoProveedor', 'LIKE', "%{$search}%");
            });
        }
        
        $datos = $query->orderBy('NITProveedores', 'asc')->paginate(10);
        
        return view('proveedores')->with('datos', $datos);
    }

    public function store(Request $request){
        $request->validate([
            'NITProveedores' => 'required|unique:proveedores,NITProveedores',
            'nombreProveedor' => 'required|string|max:45',
            'telefonoProveedor' => 'required|string|max:45',
            'correoProveedor' => 'required|email|max:45'
        ],[
             'NITProveedores.unique' => 'El NIT del proveedor ya existe en la base de datos.',
        ]);

        Proveedor::create($request->all());
        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado exitosamente');
    }

    public function edit($NITProveedores)
    {
        $proveedor = Proveedor::findOrFail($NITProveedores);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $NITProveedores){
        $proveedor = Proveedor::findOrFail($NITProveedores);
        
        $request->validate([
            'nombreProveedor' => 'required|string|max:45',
            'telefonoProveedor' => 'required|string|max:45',
            'correoProveedor' => 'required|email|max:45'
        ]);
        
        $proveedor->update([
            'nombreProveedor' => $request->nombreProveedor,
            'telefonoProveedor' => $request->telefonoProveedor,
            'correoProveedor' => $request->correoProveedor
        ]);
        
        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado exitosamente');
    }

    public function destroy($NITProveedores)
    {
        try {
            // Buscar el proveedor
            $proveedor = Proveedor::findOrFail($NITProveedores);
            
            // Verificar si hay productos asociados - CORREGIDO
            $productosAsociados = Productos::where('NITProveedores', $proveedor->NITProveedores)->exists();
            
            if ($productosAsociados) {
                return redirect()->route('proveedores.index')
                    ->with('error', 'No se puede eliminar el proveedor porque tiene productos asociados. Elimine primero los productos.');
            }
            
            // Si no hay productos asociados, eliminar el proveedor
            $proveedor->delete();
            
            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor eliminado correctamente');
                
        } catch (\Exception $e) {
            return redirect()->route('proveedores.index')
                ->with('error', 'Error al eliminar el proveedor: ' . $e->getMessage());
        }
    }
}