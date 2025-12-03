<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = Productos::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombreProducto', 'LIKE', "%{$search}%")
                ->orWhere('idProductos', 'LIKE', "%{$search}%")
                  ->orWhere('categoriaProducto', 'LIKE', "%{$search}%")
                  ->orWhere('NITProveedores', 'LIKE', "%{$search}%"); 
            });
        }
        
        $datos = $query->orderBy('idProductos', 'asc')->paginate(10);
        
        // Obtener todos los proveedores para el modal
        $proveedores = Proveedor::all();
        
        // Obtener el próximo ID
        $lastProduct = Productos::orderBy('idProductos', 'desc')->first();
        $nextId = $lastProduct ? $lastProduct->idProductos + 1 : 1;
        
        return view('productos')->with([
            'datos' => $datos,
            'proveedores' => $proveedores,
            'nextId' => $nextId
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'idProductos' => 'required|unique:productos,idProductos',
            'nombreProducto' => 'required|string|max:45',
            'entradaProducto' => 'required|integer',
            'salidaProducto' => 'required|integer',
            'categoriaProducto' => 'required|string|max:45',
            'NITProveedores' => 'required|integer|exists:proveedores,NITProveedores',
            'precioUnitario' => 'required|numeric|min:0'
        ],[
             'idProductos.unique' => 'El ID del producto ya existe en la base de datos.',
             'NITProveedores.exists' => 'El NIT del proveedor no existe en la base de datos.',
        ]);

        Productos::create($request->all());
        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente');
    }

    public function edit($idProducto) 
    {
        $producto = Productos::findOrFail($idProducto); 
        $proveedores = Proveedor::all();
        
        return view('productos.edit', compact('producto', 'proveedores')); 
    }

    public function update(Request $request, $idProducto){ 
        $producto = Productos::findOrFail($idProducto); 
        
        $request->validate([
            'nombreProducto' => 'required|string|max:45',
            'entradaProducto' => 'required|integer',
            'salidaProducto' => 'required|integer',
            'categoriaProducto' => 'required|string|max:45',
            'NITProveedores' => 'required|integer|exists:proveedores,NITProveedores', 
            'precioUnitario' => 'required|numeric|min:0'
        ]);
        
        $producto->update([
            'nombreProducto' => $request->nombreProducto,
            'entradaProducto' => $request->entradaProducto,
            'salidaProducto' => $request->salidaProducto,
            'categoriaProducto' => $request->categoriaProducto,
            'NITProveedores' => $request->NITProveedores, 
            'precioUnitario' => $request->precioUnitario
        ]);
        
        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy($idProducto){ 
        try{
            $producto = Productos::findOrFail($idProducto); 
            $producto->delete();
            return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente');
            
        }catch(\Exception $e){
            return redirect()->route('productos.index')->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }
}