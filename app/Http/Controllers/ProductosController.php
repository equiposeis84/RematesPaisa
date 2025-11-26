<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            
            $productos = Productos::when($search, function($query, $search) {
                return $query->where('nombreProducto', 'LIKE', "%{$search}%")  // ← sin 's'
                            ->orWhere('categoriaProducto', 'LIKE', "%{$search}%");  // ← sin 's'
            })
            ->orderBy('idProductos', 'asc')
            ->paginate(10);

            return view('productos', compact('productos'));

        } catch (\Exception $e) {
            Log::error('Error en índice de productos: ' . $e->getMessage());
            
            return view('productos')
                ->with('productos', collect())
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombreProducto' => 'required|string|max:255',      // ← sin 's'
                'entradaProducto' => 'required|integer|min:0',      // ← sin 's'
                'salidaProducto' => 'nullable|integer|min:0',       // ← sin 's'
                'categoriaProducto' => 'required|string|max:100',   // ← sin 's'
                'idProveedores' => 'nullable|integer',
                'precioUnitario' => 'required|numeric|min:0'        // ← "precio" no "predo"
            ]);

            $salida = $request->salidaProducto ?? 0;  // ← sin 's'

            if ($salida > $request->entradaProducto) {  // ← sin 's'
                return redirect()->back()
                    ->with('error', 'La salida no puede ser mayor que la entrada')
                    ->withInput();
            }

            Productos::create([
                'nombreProducto' => $request->nombreProducto,       // ← sin 's'
                'entradaProducto' => $request->entradaProducto,     // ← sin 's'
                'salidaProducto' => $salida,                        // ← sin 's'
                'categoriaProducto' => $request->categoriaProducto, // ← sin 's'
                'idProveedores' => $request->idProveedores,
                'precioUnitario' => $request->precioUnitario        // ← "precio" no "predo"
            ]);

            return redirect()->route('productos.index')
                ->with('success', 'Producto creado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error creando producto: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al crear producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $producto = Productos::findOrFail($id);

            $validated = $request->validate([
                'nombreProducto' => 'required|string|max:255',      // ← sin 's'
                'entradaProducto' => 'required|integer|min:0',      // ← sin 's'
                'salidaProducto' => 'nullable|integer|min:0',       // ← sin 's'
                'categoriaProducto' => 'required|string|max:100',   // ← sin 's'
                'idProveedores' => 'nullable|integer',
                'precioUnitario' => 'required|numeric|min:0'        // ← "precio" no "predo"
            ]);

            $salida = $request->salidaProducto ?? 0;  // ← sin 's'

            if ($salida > $request->entradaProducto) {  // ← sin 's'
                return redirect()->back()
                    ->with('error', 'La salida no puede ser mayor que la entrada')
                    ->withInput();
            }

            $producto->update([
                'nombreProducto' => $request->nombreProducto,       // ← sin 's'
                'entradaProducto' => $request->entradaProducto,     // ← sin 's'
                'salidaProducto' => $salida,                        // ← sin 's'
                'categoriaProducto' => $request->categoriaProducto, // ← sin 's'
                'idProveedores' => $request->idProveedores,
                'precioUnitario' => $request->precioUnitario        // ← "precio" no "predo"
            ]);

            return redirect()->route('productos.index')
                ->with('success', 'Producto actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error actualizando producto: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al actualizar producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $producto = Productos::findOrFail($id);
            $producto->delete();

            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error eliminando producto: ' . $e->getMessage());
            
            return redirect()->route('productos.index')
                ->with('error', 'Error al eliminar producto: ' . $e->getMessage());
        }
    }
}
