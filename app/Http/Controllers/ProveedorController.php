<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use App\Models\Productos;
use Illuminate\Support\Facades\DB;

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

    public function create()
    {
        // Obtener el siguiente NIT disponible
        $totalProveedores = Proveedor::count();
        $siguienteNIT = $totalProveedores + 1;
        
        // Si ya existe el NIT, buscar el siguiente disponible
        while (Proveedor::where('NITProveedores', $siguienteNIT)->exists()) {
            $siguienteNIT++;
        }
        
        return view('proveedores.create', compact('siguienteNIT'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NITProveedores' => 'required|integer|unique:proveedores,NITProveedores',
            'nombreProveedor' => 'required|string|max:45',
            'telefonoProveedor' => 'required|string|max:45',
            'correoProveedor' => 'required|email|max:45'
        ],[
            'NITProveedores.required' => 'El NIT del proveedor es obligatorio.',
            'NITProveedores.integer' => 'El NIT debe ser un número entero.',
            'NITProveedores.unique' => 'El NIT del proveedor ya existe en la base de datos.',
            'nombreProveedor.required' => 'El nombre del proveedor es obligatorio.',
            'telefonoProveedor.required' => 'El teléfono del proveedor es obligatorio.',
            'correoProveedor.required' => 'El correo del proveedor es obligatorio.',
            'correoProveedor.email' => 'El formato del correo no es válido.'
        ]);

        try {
            Proveedor::create([
                'NITProveedores' => $request->NITProveedores,
                'nombreProveedor' => $request->nombreProveedor,
                'telefonoProveedor' => $request->telefonoProveedor,
                'correoProveedor' => $request->correoProveedor
            ]);

            return redirect()->route('proveedores.index')->with('success', 'Proveedor creado exitosamente');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Error al crear el proveedor: ' . $e->getMessage())
                            ->withInput();
        }
    }

    public function edit($NITProveedores)
    {
        $proveedor = Proveedor::findOrFail($NITProveedores);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $NITProveedores)
    {
        $proveedor = Proveedor::findOrFail($NITProveedores);
        
        $request->validate([
            'nombreProveedor' => 'required|string|max:45',
            'telefonoProveedor' => 'required|string|max:45',
            'correoProveedor' => 'required|email|max:45'
        ], [
            'nombreProveedor.required' => 'El nombre del proveedor es obligatorio.',
            'telefonoProveedor.required' => 'El teléfono del proveedor es obligatorio.',
            'correoProveedor.required' => 'El correo del proveedor es obligatorio.',
            'correoProveedor.email' => 'El formato del correo no es válido.'
        ]);
        
        try {
            $proveedor->update([
                'nombreProveedor' => $request->nombreProveedor,
                'telefonoProveedor' => $request->telefonoProveedor,
                'correoProveedor' => $request->correoProveedor
            ]);
            
            return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado exitosamente');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Error al actualizar el proveedor: ' . $e->getMessage())
                            ->withInput();
        }
    }

    public function destroy($NITProveedores)
    {
        try {
            // Buscar el proveedor
            $proveedor = Proveedor::findOrFail($NITProveedores);
            
            // Verificar si hay productos asociados
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

    // Método para obtener el siguiente NIT disponible
    public function getSiguienteNIT()
    {
        $totalProveedores = Proveedor::count();
        $siguienteNIT = $totalProveedores + 1;
        
        // Si ya existe el NIT, buscar el siguiente disponible
        while (Proveedor::where('NITProveedores', $siguienteNIT)->exists()) {
            $siguienteNIT++;
        }
        
        return response()->json(['siguienteNIT' => $siguienteNIT]);
    }
}