<?php

namespace App\Http\Controllers;

use App\Models\Productos;
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
                  ->orWhere('idProveedores', 'LIKE', "%{$search}%");
            });
        }
        
        $datos = $query->orderBy('idProductos', 'asc')->paginate(10);
        
        return view('productos')->with('datos', $datos);
    }

    public function store(Request $request){
        $request->validate([
            'idProductos' => 'required|unique:productos,idProductos',
            'nombreProducto' => 'required|string|max:45',
            'entradaProducto' => 'required|integer',
            'salidaProducto' => 'required|integer',
            'categoriaProducto' => 'required|string|max:45',
            'idProveedores' => 'required|integer',
            'precioUnitario' => 'required|numeric|min:0'
        ],[
             'idProductos.unique' => 'El ID del producto ya existe en la base de datos.',
        ]);

        Productos::create($request->all());
        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente');
    }

    public function edit($idProductos)
    {
        $producto = Productos::findOrFail($idProductos);
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, $idProductos){
        $producto = Productos::findOrFail($idProductos);
        
        $request->validate([
            'nombreProducto' => 'required|string|max:45',
            'entradaProducto' => 'required|integer',
            'salidaProducto' => 'required|integer',
            'categoriaProducto' => 'required|string|max:45',
            'idProveedores' => 'required|integer',
            'precioUnitario' => 'required|numeric|min:0'
        ]);
        
        $producto->update([
            'nombreProducto' => $request->nombreProducto,
            'entradaProducto' => $request->entradaProducto,
            'salidaProducto' => $request->salidaProducto,
            'categoriaProducto' => $request->categoriaProducto,
            'idProveedores' => $request->idProveedores,
            'precioUnitario' => $request->precioUnitario
        ]);
        
        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy($idProductos){
        try{
            $producto = Productos::findOrFail($idProductos); 
            $producto->delete();
            return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente');
            
        }catch(\Exception $e){
            return redirect()->route('productos.index')->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }
}