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
// CRUD PEDIDOS (Admin)
// -------------------------------------------------------------------------
Route::prefix('pedidos')->name('pedidos.')->group(function () {
    Route::get('/', function (Request $request) { 
        $error = protegerRuta(1); 
        if ($error) return $error; 
        return app(PedidosController::class)->index($request); 
    })->name('index');
    
    Route::post('/', function (Request $request) { 
        $error = protegerRuta(1); 
        if ($error) return $error; 
        return app(PedidosController::class)->store($request); 
    })->name('store');
    
    Route::get('/create', function (Request $request) { 
        $error = protegerRuta(1); 
        if ($error) return $error; 
        return app(PedidosController::class)->create($request); 
    })->name('create');
    
    Route::get('/{pedido}', function (Request $request, $pedido) { 
        $error = protegerRuta(1); 
        if ($error) return $error; 
        return app(PedidosController::class)->show($request, $pedido); 
    })->name('show');
    
    Route::get('/{pedido}/edit', function (Request $request, $pedido) { 
        $error = protegerRuta(1); 
        if ($error) return $error; 
        return app(PedidosController::class)->edit($request, $pedido); 
    })->name('edit');
    
    Route::put('/{pedido}', function (Request $request, $pedido) { 
        $error = protegerRuta(1); 
        if ($error) return $error; 
        return app(PedidosController::class)->update($request, $pedido); 
    })->name('update');
    
    Route::delete('/{pedido}', function (Request $request, $pedido) { 
        $error = protegerRuta(1); 
        if ($error) return $error; 
        return app(PedidosController::class)->destroy($request, $pedido); 
    })->name('destroy');
    
    // =========================================================================
    // PRODUCTOS DE LOS PEDIDOS (Detalle de productos)
    // =========================================================================
    Route::prefix('{idPedido}/productos')->name('productos.')->group(function () {
        Route::get('/', function (Request $request, $idPedido) {
            $error = protegerRuta(1);
            if ($error) return $error;
            return app(ProductosPedidoController::class)->index($idPedido);
        })->name('index');
        
        Route::post('/', function (Request $request, $idPedido) {
            $error = protegerRuta(1);
            if ($error) return $error;
            return app(ProductosPedidoController::class)->store($request, $idPedido);
        })->name('store');
        
        Route::get('/create', function (Request $request, $idPedido) {
            $error = protegerRuta(1);
            if ($error) return $error;
            return app(ProductosPedidoController::class)->create($idPedido);
        })->name('create');
        
        Route::get('/{idProducto}', function (Request $request, $idPedido, $idProducto) {
            $error = protegerRuta(1);
            if ($error) return $error;
            return app(ProductosPedidoController::class)->show($idPedido, $idProducto);
        })->name('show');
        
        Route::get('/{idProducto}/edit', function (Request $request, $idPedido, $idProducto) {
            $error = protegerRuta(1);
            if ($error) return $error;
            return app(ProductosPedidoController::class)->edit($idPedido, $idProducto);
        })->name('edit');
        
        Route::put('/{idProducto}', function (Request $request, $idPedido, $idProducto) {
            $error = protegerRuta(1);
            if ($error) return $error;
            return app(ProductosPedidoController::class)->update($request, $idPedido, $idProducto);
        })->name('update');
        
        Route::delete('/{idProducto}', function (Request $request, $idPedido, $idProducto) {
            $error = protegerRuta(1);
            if ($error) return $error;
            return app(ProductosPedidoController::class)->destroy($idPedido, $idProducto);
        })->name('destroy');
    });
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
// CRUD PROVEEDORES (Admin) - AÑADIDAS
// -------------------------------------------------------------------------
Route::get('/proveedores', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProveedorController::class)->index($request); 
})->name('proveedores.index');
Route::post('/proveedores', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProveedorController::class)->store($request); 
})->name('proveedores.store');
Route::get('/proveedores/create', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProveedorController::class)->create($request); 
})->name('proveedores.create');
Route::get('/proveedores/{proveedor}', function (Request $request, $proveedor) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProveedorController::class)->show($request, $proveedor); 
})->name('proveedores.show');
Route::get('/proveedores/{proveedor}/edit', function (Request $request, $proveedor) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProveedorController::class)->edit($request, $proveedor); 
})->name('proveedores.edit');
Route::put('/proveedores/{proveedor}', function (Request $request, $proveedor) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProveedorController::class)->update($request, $proveedor); 
})->name('proveedores.update');
Route::delete('/proveedores/{proveedor}', function (Request $request, $proveedor) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProveedorController::class)->destroy($request, $proveedor); 
})->name('proveedores.destroy');
Route::get('/proveedores/siguiente-nit', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(ProveedorController::class)->getSiguienteNIT($request); 
})->name('proveedores.getSiguienteNIT');

// -------------------------------------------------------------------------
// CRUD ROLES (Admin) - AÑADIDAS
// -------------------------------------------------------------------------
Route::get('/roles', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(RolesController::class)->index($request); 
})->name('roles.index');
Route::post('/roles', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(RolesController::class)->store($request); 
})->name('roles.store');
Route::get('/roles/{rol}', function (Request $request, $rol) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(RolesController::class)->show($request, $rol); 
})->name('roles.show');
Route::get('/roles/{rol}/edit', function (Request $request, $rol) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(RolesController::class)->edit($request, $rol); 
})->name('roles.edit');
Route::put('/roles/{rol}', function (Request $request, $rol) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(RolesController::class)->update($request, $rol); 
})->name('roles.update');
Route::delete('/roles/{rol}', function (Request $request, $rol) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(RolesController::class)->destroy($request, $rol); 
})->name('roles.destroy');

// -------------------------------------------------------------------------
// CRUD USUARIOS (Admin) - AÑADIDAS
// -------------------------------------------------------------------------
Route::get('/usuarios', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(UsuariosController::class)->index($request); 
})->name('usuarios.index');
Route::post('/usuarios', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(UsuariosController::class)->store($request); 
})->name('usuarios.store');
Route::get('/usuarios/{usuario}', function (Request $request, $usuario) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(UsuariosController::class)->show($request, $usuario); 
})->name('usuarios.show');
Route::get('/usuarios/{usuario}/edit', function (Request $request, $usuario) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(UsuariosController::class)->edit($request, $usuario); 
})->name('usuarios.edit');
Route::put('/usuarios/{usuario}', function (Request $request, $usuario) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(UsuariosController::class)->update($request, $usuario); 
})->name('usuarios.update');
Route::delete('/usuarios/{usuario}', function (Request $request, $usuario) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(UsuariosController::class)->destroy($request, $usuario); 
})->name('usuarios.destroy');

// -------------------------------------------------------------------------
// CRUD AYUDA Y CONTACTO (Admin) - AÑADIDAS
// -------------------------------------------------------------------------
Route::get('/ayuda-contacto', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(AyudaContactoController::class)->index($request); 
})->name('AyudaContacto.index');
Route::post('/ayuda-contacto', function (Request $request) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(AyudaContactoController::class)->store($request); 
})->name('AyudaContacto.store');
Route::get('/ayuda-contacto/{ayudaContacto}', function (Request $request, $ayudaContacto) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(AyudaContactoController::class)->show($request, $ayudaContacto); 
})->name('AyudaContacto.show');
Route::get('/ayuda-contacto/{ayudaContacto}/edit', function (Request $request, $ayudaContacto) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(AyudaContactoController::class)->edit($request, $ayudaContacto); 
})->name('AyudaContacto.edit');
Route::put('/ayuda-contacto/{ayudaContacto}', function (Request $request, $ayudaContacto) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(AyudaContactoController::class)->update($request, $ayudaContacto); 
})->name('AyudaContacto.update');
Route::delete('/ayuda-contacto/{ayudaContacto}', function (Request $request, $ayudaContacto) { 
    $error = protegerRuta(1); 
    if ($error) return $error; 
    return app(AyudaContactoController::class)->destroy($request, $ayudaContacto); 
})->name('AyudaContacto.destroy');

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

// Logout seguro (POST)
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