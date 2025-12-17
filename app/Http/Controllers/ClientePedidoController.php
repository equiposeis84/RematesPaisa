<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pedidos;
use App\Models\Productos;
use App\Models\DetallePedido;
use App\Models\Usuario;

class ClientePedidoController extends Controller
{
    /**
     * Mostrar formulario de checkout
     */
    public function checkout()
    {
        // Verificar que el usuario esté logueado
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para realizar un pedido');
        }
        
        $carrito = session('carrito', []);
        
        if (empty($carrito)) {
            return redirect()->route('catalogo')->with('error', 'Tu carrito está vacío');
        }
        
        // Calcular totales
        $subtotal = 0;
        $totalItems = 0;
        foreach ($carrito as $item) {
            $subtotal += $item['subtotal'];
            $totalItems += $item['cantidad'];
        }
        
        $iva = $subtotal * 0.19;
        $total = $subtotal + $iva;
        
        // Obtener datos del usuario
        $usuario = Usuario::find(session('user_id'));
        
        // IMPORTANTE: Usar vista 'checkout' (sin carpeta pedidos)
        return view('checkout', [
            'carrito' => $carrito,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total,
            'totalItems' => $totalItems,
            'usuario' => $usuario
        ]);
    }
    
    /**
     * Procesar el pedido desde el carrito
     */
    public function procesar(Request $request)
    {
        // Verificar que el usuario esté logueado
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para realizar un pedido');
        }
        
        // Validar datos del pedido
        $request->validate([
            'direccion' => 'required|string|min:10|max:255',
            'telefono' => 'required|string|min:7|max:20',
            'observaciones' => 'nullable|string|max:500',
            'metodo_pago' => 'required|in:efectivo,transferencia'
        ]);
        
        $carrito = session('carrito', []);
        
        if (empty($carrito)) {
            return redirect()->route('catalogo')->with('error', 'Tu carrito está vacío');
        }
        
        $usuario = Usuario::find(session('user_id'));
        if (!$usuario) {
            return redirect()->route('catalogo')->with('error', 'Usuario no encontrado');
        }
        
        // Verificar stock antes de procesar
        foreach ($carrito as $id => $item) {
            $producto = Productos::find($id);
            if (!$producto) {
                return back()->with('error', "El producto '{$item['nombre']}' ya no existe");
            }
            
            $stockDisponible = $producto->entradaProducto - $producto->salidaProducto;
            if ($stockDisponible < $item['cantidad']) {
                return back()->with('error', 
                    "Stock insuficiente para: {$producto->nombreProducto}. Solo quedan {$stockDisponible} unidades.");
            }
        }
        
        // Calcular totales
        $subtotal = 0;
        foreach ($carrito as $item) {
            $subtotal += $item['subtotal'];
        }
        $iva = $subtotal * 0.19;
        $totalConIva = $subtotal + $iva;
        
        // Iniciar transacción
        DB::beginTransaction();
        
        try {
            // Generar ID único para el pedido
            $lastPedido = Pedidos::orderBy('idPedidos', 'desc')->first();
            $nextId = $lastPedido ? $lastPedido->idPedidos + 1 : 1;
            
            // Crear el pedido principal
            $pedido = Pedidos::create([
                'idPedidos' => $nextId,
                'fechaPedido' => now()->format('Y-m-d'),
                'horaPedido' => now()->format('H:i:s'),
                'documento' => $usuario->documento, // Usar documento del usuario
                'valorPedido' => $subtotal,
                'ivaPedido' => $iva,
                'totalPedido' => $totalConIva,
                'estadoPedido' => 'pendiente',
                'repartidorPedido' => null,
                'direccion_entrega' => $request->direccion,
                'telefono_contacto' => $request->telefono,
                'observaciones_cliente' => $request->observaciones,
                'metodo_pago' => $request->metodo_pago,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Crear detalles del pedido y actualizar stock
            foreach ($carrito as $id => $item) {
                $producto = Productos::find($id);
                
                // Crear detalle del pedido
                DetallePedido::create([
                    'idPedido' => $pedido->idPedidos,
                    'idProducto' => $id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['subtotal']
                ]);
                
                // Actualizar stock del producto
                $producto->salidaProducto += $item['cantidad'];
                $producto->save();
            }
            
            // Confirmar transacción
            DB::commit();
            
            // Vaciar el carrito
            session()->forget('carrito');
            
            // Redirigir con éxito
            return redirect()->route('usuario.pedidos')
                ->with('success', '¡Pedido realizado con éxito! Número de pedido: ' . $pedido->idPedidos);
                
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            
            return back()->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }
    
    /**
     * Ver pedidos del usuario
     */
    public function misPedidos(Request $request)
    {
        // Verificar que el usuario esté logueado
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus pedidos');
        }
        
        $usuario = Usuario::find(session('user_id'));
        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Usuario no encontrado');
        }
        
        $search = $request->get('search', '');
        
        // Obtener pedidos del usuario por su documento
        $query = Pedidos::where('documento', $usuario->documento)
            ->orderBy('fechaPedido', 'desc')
            ->orderBy('horaPedido', 'desc');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('idPedidos', 'LIKE', "%{$search}%")
                  ->orWhere('estadoPedido', 'LIKE', "%{$search}%")
                  ->orWhere('repartidorPedido', 'LIKE', "%{$search}%");
            });
        }
        
        $pedidos = $query->paginate(10);
        
        return view('pedidos.mis-pedidos', [
            'pedidos' => $pedidos,
            'search' => $search,
            'usuario' => $usuario
        ]);
    }
    
    /**
     * Ver detalle de un pedido específico del usuario
     */
    public function verPedido($idPedidos)
    {
        // Verificar que el usuario esté logueado
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }
        
        $usuario = Usuario::find(session('user_id'));
        
        // Obtener pedido verificando que pertenezca al usuario
        $pedido = Pedidos::where('idPedidos', $idPedidos)
            ->where('documento', $usuario->documento)
            ->firstOrFail();
        
        // Obtener detalles del pedido
        $detalles = DetallePedido::where('idPedido', $idPedidos)
            ->join('productos', 'detalle_pedido.idProducto', '=', 'productos.idProductos')
            ->select('detalle_pedido.*', 'productos.nombreProducto', 'productos.categoriaProducto')
            ->get();
        
        return view('pedidos.ver-pedido', [
            'pedido' => $pedido,
            'detalles' => $detalles,
            'usuario' => $usuario
        ]);
    }
}