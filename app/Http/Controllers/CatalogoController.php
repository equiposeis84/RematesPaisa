<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    /**
     * Mostrar el catálogo de productos (ACCESO PÚBLICO)
     */
    public function index(Request $request)
    {
        // Obtener parámetros de búsqueda
        $search = $request->input('search', '');
        $categoria = $request->input('categoria', '');

        // Consulta base de productos
        $query = Productos::select(
                'productos.idProductos',
                'productos.nombreProducto',
                'productos.categoriaProducto',
                'productos.precioUnitario',
                'productos.entradaProducto',
                'productos.salidaProducto',
                DB::raw('(productos.entradaProducto - productos.salidaProducto) as stock')
            )
            ->where(DB::raw('(productos.entradaProducto - productos.salidaProducto)'), '>', 0); // Solo productos con stock

        // Aplicar filtros
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('productos.nombreProducto', 'like', "%{$search}%")
                  ->orWhere('productos.categoriaProducto', 'like', "%{$search}%");
            });
        }

        if (!empty($categoria)) {
            $query->where('productos.categoriaProducto', $categoria);
        }

        // Ordenar y paginar
        $productos = $query->orderBy('productos.nombreProducto', 'asc')
                          ->paginate(12);

        // Obtener categorías únicas para filtro
        $categorias = Productos::select('categoriaProducto')
            ->distinct()
            ->orderBy('categoriaProducto', 'asc')
            ->pluck('categoriaProducto');

        // Verificar si hay una pestaña activa
        $activeTab = $request->session()->get('activeTab', 'catalogo');
        
        // Limpiar la sesión después de usarla
        $request->session()->forget('activeTab');

        return view('catalogo', compact('productos', 'search', 'categoria', 'categorias', 'activeTab'));
    }

    /**
     * Añadir producto al carrito (funciona sin sesión)
     */
    public function addToCart(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'idProducto' => 'required|exists:productos,idProductos',
            'cantidad' => 'required|integer|min:1'
        ]);

        // Obtener producto
        $producto = Productos::find($request->idProducto);
        
        // Verificar stock
        $stock = $producto->entradaProducto - $producto->salidaProducto;
        if ($request->cantidad > $stock) {
            return response()->json([
                'success' => false,
                'message' => 'No hay suficiente stock disponible'
            ], 400);
        }

        // Inicializar carrito en sesión si no existe
        if (!session()->has('carrito')) {
            session(['carrito' => []]);
        }

        $carrito = session('carrito');
        $idProducto = $request->idProducto;
        $cantidad = $request->cantidad;

        // Verificar si el producto ya está en el carrito
        if (isset($carrito[$idProducto])) {
            $nuevaCantidad = $carrito[$idProducto]['cantidad'] + $cantidad;
            
            // Verificar stock nuevamente
            if ($nuevaCantidad > $stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes añadir más unidades, stock insuficiente'
                ], 400);
            }
            
            $carrito[$idProducto]['cantidad'] = $nuevaCantidad;
            $carrito[$idProducto]['subtotal'] = $nuevaCantidad * $producto->precioUnitario;
        } else {
            // Añadir nuevo producto al carrito
            $carrito[$idProducto] = [
                'id' => $producto->idProductos,
                'nombre' => $producto->nombreProducto,
                'precio' => $producto->precioUnitario,
                'cantidad' => $cantidad,
                'subtotal' => $cantidad * $producto->precioUnitario,
                'categoria' => $producto->categoriaProducto,
                'stock' => $stock
            ];
        }

        // Actualizar sesión
        session(['carrito' => $carrito]);

        // Calcular totales
        $totalItems = array_sum(array_column($carrito, 'cantidad'));
        $totalPrecio = array_sum(array_column($carrito, 'subtotal'));

        return response()->json([
            'success' => true,
            'message' => 'Producto añadido al carrito',
            'carrito' => $carrito,
            'totalItems' => $totalItems,
            'totalPrecio' => number_format($totalPrecio, 2),
            'producto' => $carrito[$idProducto]
        ]);
    }

    /**
     * Ver detalles del producto (ACCESO PÚBLICO)
     */
    public function show($id)
    {
        $producto = Productos::select(
                'productos.*',
                'proveedores.nombreProveedor',
                DB::raw('(productos.entradaProducto - productos.salidaProducto) as stock')
            )
            ->leftJoin('proveedores', 'productos.NITProveedores', '=', 'proveedores.NITProveedores')
            ->findOrFail($id);

        // Productos relacionados (misma categoría)
        $relacionados = Productos::where('categoriaProducto', $producto->categoriaProducto)
            ->where('idProductos', '!=', $id)
            ->where(DB::raw('(entradaProducto - salidaProducto)'), '>', 0)
            ->limit(4)
            ->get();

        return view('catalogo', [
            'producto' => $producto,
            'relacionados' => $relacionados,
            'activeTab' => 'detalle'
        ]);
    }

    /**
     * Obtener carrito actual (funciona sin sesión)
     */
    public function getCart()
    {
        $carrito = session('carrito', []);
        $totalItems = array_sum(array_column($carrito, 'cantidad'));
        $totalPrecio = array_sum(array_column($carrito, 'subtotal'));

        return response()->json([
            'success' => true,
            'carrito' => $carrito,
            'totalItems' => $totalItems,
            'totalPrecio' => number_format($totalPrecio, 2),
            'contador' => count($carrito)
        ]);
    }

    /**
     * Actualizar cantidad en carrito (funciona sin sesión)
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'idProducto' => 'required|exists:productos,idProductos',
            'cantidad' => 'required|integer|min:1'
        ]);

        $carrito = session('carrito', []);
        $idProducto = $request->idProducto;

        if (!isset($carrito[$idProducto])) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito'], 404);
        }

        // Obtener producto para verificar stock
        $producto = Productos::find($idProducto);
        $stock = $producto->entradaProducto - $producto->salidaProducto;

        if ($request->cantidad > $stock) {
            return response()->json([
                'success' => false,
                'message' => 'No hay suficiente stock disponible'
            ], 400);
        }

        // Actualizar cantidad
        $carrito[$idProducto]['cantidad'] = $request->cantidad;
        $carrito[$idProducto]['subtotal'] = $request->cantidad * $carrito[$idProducto]['precio'];

        // Actualizar sesión
        session(['carrito' => $carrito]);

        // Calcular nuevos totales
        $totalItems = array_sum(array_column($carrito, 'cantidad'));
        $totalPrecio = array_sum(array_column($carrito, 'subtotal'));

        return response()->json([
            'success' => true,
            'message' => 'Carrito actualizado',
            'carrito' => $carrito,
            'totalItems' => $totalItems,
            'totalPrecio' => number_format($totalPrecio, 2),
            'item' => $carrito[$idProducto]
        ]);
    }

    /**
     * Eliminar producto del carrito (funciona sin sesión)
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'idProducto' => 'required|exists:productos,idProductos'
        ]);

        $carrito = session('carrito', []);
        $idProducto = $request->idProducto;

        if (isset($carrito[$idProducto])) {
            $productoEliminado = $carrito[$idProducto];
            unset($carrito[$idProducto]);
            
            session(['carrito' => $carrito]);

            // Calcular nuevos totales
            $totalItems = array_sum(array_column($carrito, 'cantidad'));
            $totalPrecio = array_sum(array_column($carrito, 'subtotal'));

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'carrito' => $carrito,
                'totalItems' => $totalItems,
                'totalPrecio' => number_format($totalPrecio, 2),
                'productoEliminado' => $productoEliminado
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Producto no encontrado en el carrito'], 404);
    }

    /**
     * Vaciar carrito (funciona sin sesión)
     */
    public function clearCart()
    {
        session(['carrito' => []]);
        
        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado correctamente',
            'carrito' => [],
            'totalItems' => 0,
            'totalPrecio' => '0.00'
        ]);
    }
}