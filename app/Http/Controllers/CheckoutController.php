<?php

namespace App\Http\Controllers;

use App\Models\Pedidos;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    // Mostrar la página de checkout
    public function checkout(Request $request)
    {
        \Log::info('=== CHECKOUT INICIADO ===');
        \Log::info('Session ID:', ['session_id' => session()->getId()]);
        \Log::info('User ID en sesión:', ['user_id' => session('user_id')]);
        \Log::info('User Type en sesión:', ['user_type' => session('user_type')]);
        
        $carrito = session()->get('carrito', []);
        \Log::info('Carrito obtenido (clave: carrito):', $carrito);

        // Verificar que el usuario esté logueado
        if (!session()->has('user_id')) {
            \Log::warning('Redirigiendo a login - No hay user_id en sesión');
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para realizar un pedido');
        }

        // Verificar que no sea admin (solo clientes)
        if (session('user_type') == 1) {
            \Log::warning('Redirigiendo a admin - Usuario es admin');
            return redirect()->route('admin.inicio')
                ->with('error', 'Los administradores no pueden realizar pedidos');
        }

        // Verificar que el carrito no esté vacío
        if (empty($carrito)) {
            \Log::warning('Redirigiendo a catálogo - Carrito vacío');
            return redirect()->route('catalogo')
                ->with('error', 'Tu carrito está vacío');
        }

        // Obtener información del usuario
        $usuario = Usuario::find(session('user_id'));
        
        if (!$usuario) {
            \Log::error('Redirigiendo a login - Usuario no encontrado en BD');
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Sesión expirada o usuario no encontrado');
        }
        
        \Log::info('Usuario encontrado:', ['id' => $usuario->idUsuario, 'nombre' => $usuario->nombre]);

        // Calcular totales
        $subtotal = 0;
        foreach ($carrito as $id => $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }
        $iva = $subtotal * 0.19;
        $total = $subtotal + $iva;
        
        \Log::info('Totales calculados:', ['subtotal' => $subtotal, 'iva' => $iva, 'total' => $total]);

        // CORRECCIÓN: Usar la vista en VistasCliente
        return view('VistasCliente.checkout', [
            'carrito' => $carrito,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total,
            'usuario' => $usuario
        ]);
    }

    // Procesar el pedido
    public function procesar(Request $request)
    {
        // Verificar autenticación
        if (!session()->has('user_id')) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        if (session('user_type') == 1) {
            return response()->json(['error' => 'Acceso denegado'], 403);
        }

        try {
            DB::beginTransaction();
            Log::info('Iniciando procesamiento de pedido');
            Log::info('Usuario ID: ' . session('user_id'));

            $carrito = session()->get('carrito', []);
            Log::info('Carrito (clave: carrito): ' . json_encode($carrito));
            
            if (empty($carrito)) {
                return response()->json(['error' => 'Carrito vacío'], 400);
            }

            // Obtener usuario
            $usuario = Usuario::find(session('user_id'));
            
            if (!$usuario) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // Verificar documento
            if (empty($usuario->documento)) {
                return response()->json([
                    'error' => 'Tu perfil no tiene documento registrado.'
                ], 400);
            }

            // Calcular totales
            $subtotal = 0;
            foreach ($carrito as $item) {
                $subtotal += $item['precio'] * $item['cantidad'];
            }
            $iva = $subtotal * 0.19;
            $total = $subtotal + $iva;

            // Obtener siguiente ID de pedido
            $ultimoPedido = Pedidos::orderBy('idPedidos', 'desc')->first();
            $nuevoId = $ultimoPedido ? $ultimoPedido->idPedidos + 1 : 1;

            // Crear el pedido
            $pedido = Pedidos::create([
                'idPedidos' => $nuevoId,
                'fechaPedido' => now()->format('Y-m-d'),
                'horaPedido' => now()->format('H:i:s'),
                'documento' => $usuario->documento,
                'valorPedido' => $subtotal,
                'ivaPedido' => $iva,
                'totalPedido' => $total,
                'estadoPedido' => 'Pendiente',
                'repartidorPedido' => null
            ]);

            Log::info('Pedido creado: ' . $nuevoId);

            // Insertar productos del pedido
            foreach ($carrito as $idProducto => $item) {
                $ivaProducto = ($item['precio'] * $item['cantidad']) * 0.19;
                $totalProducto = ($item['precio'] * $item['cantidad']) + $ivaProducto;
                
                DB::table('detalleproductos')->insert([
                    'idPedido' => $nuevoId,
                    'idProductos' => $idProducto,
                    'cantidadDetalleProducto' => $item['cantidad'],
                    'valorUnitarioDetalleProducto' => $item['precio'],
                    'totalPagarDetalleProducto' => $item['precio'] * $item['cantidad'],
                    'ivaDetalleProducto' => $ivaProducto,
                    'totalDetalleProducto' => $totalProducto
                ]);
                
                Log::info('Producto añadido: ' . $idProducto . ' x' . $item['cantidad']);
            }

            // Limpiar carrito
            session()->forget('carrito');

            DB::commit();

            Log::info('Pedido procesado exitosamente: ' . $nuevoId);

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'pedido_id' => $nuevoId,
                'redirect' => route('usuario.mis-pedidos')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al procesar pedido: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Error al procesar el pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    // Ver pedidos del usuario
    public function misPedidos(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $usuario = Usuario::find(session('user_id'));
        
        if (!$usuario) {
            return redirect()->route('login');
        }
        
        $pedidos = Pedidos::where('documento', $usuario->documento)
            ->orderBy('fechaPedido', 'desc')
            ->orderBy('horaPedido', 'desc')
            ->paginate(10);

        return view('VistasCliente.mis-pedidos', compact('pedidos', 'usuario'));
    }
}