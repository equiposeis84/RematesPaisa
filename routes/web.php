<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductosController; 
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UsuariosController; 
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ProductosPedidoController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\AyudaContactoController;

// =============================================================================
// RUTAS PÚBLICAS (ACCESIBLES SIN AUTENTICACIÓN)
// =============================================================================

// Ruta principal
Route::get('/', function () { 
    if (session()->has('user_id')) {
        $role = session('user_type');
        if ($role == 1) return redirect()->route('admin.inicio');
        else return redirect()->route('catalogo');
    }
    return view('index');
});

// -----------------------------------------------------------------------------
// AUTENTICACIÓN (Público)
// -----------------------------------------------------------------------------
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', function () {
    if (session()->has('user_id')) {
        return redirect()->route('catalogo');
    }
    return view('auth.register');
})->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// =============================================================================
// RUTAS CON PROTECCIÓN MANUAL
// =============================================================================

// -----------------------------------------------------------------------------
// FUNCIÓN DE PROTECCIÓN REUTILIZABLE
// -----------------------------------------------------------------------------
function protegerRuta($roleRequerido = null) {
    if (!session()->has('user_id')) return redirect()->route('login')->with('error', 'Debes iniciar sesión');
    if ($roleRequerido && session('user_type') != $roleRequerido) {
        return session('user_type') == 1
            ? redirect()->route('admin.inicio')->with('error', 'Acceso restringido')
            : redirect()->route('catalogo')->with('error', 'Acceso restringido a administradores');
    }
    return null;
}

// =============================================================================
// RUTAS PARA CLIENTES/REPARTIDORES (Rol 2 o 3)
// =============================================================================

// Catálogo principal para clientes
Route::get('/catalogo', function () {
    $error = protegerRuta(); 
    if ($error) return $error;
    if (session('user_type') == 1) return redirect()->route('admin.inicio');
    return view('index');
})->name('catalogo');

// Rutas de usuario (cliente)
Route::prefix('usuario')->name('usuario.')->group(function () {
    Route::get('/catalogo', function () { 
        return redirect()->route('catalogo');
    })->name('catalogo');
    
    Route::get('/carrito', function () { 
        $error = protegerRuta(); 
        if ($error) return $error; 
        if (session('user_type') == 1) return redirect()->route('admin.inicio');
        return view('Usuarios.CarritoCompras'); 
    })->name('carrito');
    
    Route::get('/ayuda-contacto', function () { 
        $error = protegerRuta(); 
        if ($error) return $error; 
        if (session('user_type') == 1) return redirect()->route('admin.inicio');
        return view('Usuarios.AyudaContacto'); 
    })->name('ayuda.contacto');
    
    Route::get('/pedidos', function () { 
        $error = protegerRuta(); 
        if ($error) return $error; 
        if (session('user_type') == 1) return redirect()->route('admin.inicio');
        return view('Usuarios.PedidosUsuario'); 
    })->name('pedidos');
});

// =============================================================================
// RUTAS PARA ADMINISTRADORES (SOLO Rol 1)
// =============================================================================

// Panel de administración
Route::get('/admin/inicio', function () { 
    $error = protegerRuta(1); 
    if ($error) return $error;
    return view('VistasAdmin.welcome');
})->name('admin.inicio');

Route::get('/admin', function () { 
    return redirect()->route('admin.inicio'); 
});

// -------------------------------------------------------------------------
// CRUD CLIENTES (Admin)
// -------------------------------------------------------------------------
Route::get('/clientes', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ClienteController::class)->index($request); 
})->name('clientes.index');
Route::post('/clientes', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ClienteController::class)->store($request); 
})->name('clientes.store');
Route::get('/clientes/{idCliente}', function (Request $request, $idCliente) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ClienteController::class)->edit($request, $idCliente); 
})->name('clientes.edit');
Route::put('/clientes/{idCliente}', function (Request $request, $idCliente) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ClienteController::class)->update($request, $idCliente); 
})->name('clientes.update');
Route::delete('/clientes/{idCliente}', function (Request $request, $idCliente) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ClienteController::class)->destroy($request, $idCliente); 
})->name('clientes.destroy');

// -------------------------------------------------------------------------
// CRUD PRODUCTOS (Admin)
// -------------------------------------------------------------------------
Route::get('/productos', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProductosController::class)->index($request); 
})->name('productos.index');
Route::post('/productos', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProductosController::class)->store($request); 
})->name('productos.store');
Route::get('/productos/{idProducto}', function (Request $request, $idProducto) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProductosController::class)->edit($request, $idProducto); 
})->name('productos.edit');
Route::put('/productos/{idProducto}', function (Request $request, $idProducto) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProductosController::class)->update($request, $idProducto); 
})->name('productos.update');
Route::delete('/productos/{idProducto}', function (Request $request, $idProducto) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProductosController::class)->destroy($request, $idProducto); 
})->name('productos.destroy');
Route::get('/productos/next-id', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProductosController::class)->getNextProductId($request); 
})->name('productos.next.id');

// -------------------------------------------------------------------------
// CRUD PEDIDOS (Admin)
// -------------------------------------------------------------------------
Route::get('/pedidos', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(PedidosController::class)->index($request); 
})->name('pedidos.index');
Route::post('/pedidos', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(PedidosController::class)->store($request); 
})->name('pedidos.store');
Route::get('/pedidos/create', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(PedidosController::class)->create($request); 
})->name('pedidos.create');
Route::get('/pedidos/{pedido}', function (Request $request, $pedido) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(PedidosController::class)->show($request, $pedido); 
})->name('pedidos.show');
Route::get('/pedidos/{pedido}/edit', function (Request $request, $pedido) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(PedidosController::class)->edit($request, $pedido); 
})->name('pedidos.edit');
Route::put('/pedidos/{pedido}', function (Request $request, $pedido) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(PedidosController::class)->update($request, $pedido); 
})->name('pedidos.update');
Route::delete('/pedidos/{pedido}', function (Request $request, $pedido) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(PedidosController::class)->destroy($request, $pedido); 
})->name('pedidos.destroy');

// ... (MANTÉN TODAS TUS OTRAS RUTAS ADMIN AQUÍ EXACTAMENTE COMO LAS TENÍAS) ...

// =============================================================================
// RUTAS COMPARTIDAS PARA TODOS LOS AUTENTICADOS
// =============================================================================

Route::get('/bienvenido', function () { 
    $error = protegerRuta(); 
    if ($error) return $error; 
    return view('VistasAdmin.welcome'); 
})->name('welcome');

Route::get('/dashboard', function () { 
    $error = protegerRuta(); 
    if ($error) return $error; 
    return view('dashboard'); 
})->name('dashboard');

// =============================================================================
// RUTAS DE AUTENTICACIÓN
// =============================================================================

// Logout seguro (POST) - ESTA ES LA CLAVE
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Logout temporal GET (para desarrollo)
Route::get('/logout-get', function() {
    if (session()->has('user_id')) {
        $nombre = session('user_name');
        session()->flush();
        return redirect('/')->with('success', "Sesión cerrada. ¡Adiós $nombre!");
    }
    return redirect('/')->with('info', 'No había sesión activa');
})->name('logout.get');

// Limpiar sesión (para desarrollo)
Route::get('/limpiar-sesion', function() {
    session()->flush();
    return redirect('/')->with('success', 'Sesión limpiada correctamente');
});

// Ruta para debug
Route::get('/debug-routes', function() {
    echo '<h3>Rutas disponibles:</h3>';
    echo '<pre>';
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ];
    });
    print_r($routes->toArray());
    echo '</pre>';
});