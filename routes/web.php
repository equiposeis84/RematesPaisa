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
use App\Http\Controllers\AuthController;

// =============================================================================
// RUTAS PÚBLICAS (ACCESIBLES SIN AUTENTICACIÓN)
// =============================================================================

// Ruta principal - Con redirección inteligente
Route::get('/', function () { 
    if (session()->has('user_id')) {
        $role = session('user_type');
        if ($role == 1) return redirect()->route('admin.inicio');
        else return redirect()->route('catalogo');
    }
    return view('VistasCliente.inicio');
});

// -----------------------------------------------------------------------------
// AUTENTICACIÓN (Público)
// -----------------------------------------------------------------------------
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
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
        if (session('user_type') == 1) return redirect()->route('admin.inicio')->with('error', 'Acceso restringido');
        else return redirect()->route('catalogo')->with('error', 'Acceso restringido a administradores');
    }
    return null;
}

// =============================================================================
// RUTAS PARA CLIENTES/REPARTIDORES (Rol 2 o 3)
// =============================================================================

// Catálogo principal
Route::get('/catalogo', function () {
    $error = protegerRuta(); if ($error) return $error;
    if (session('user_type') == 1) return redirect()->route('admin.inicio');
    return view('index');
})->name('catalogo');

// Rutas específicas de cliente
Route::get('/cliente/catalogo', function () {
    $error = protegerRuta(2); if ($error) return $error;
    return view('index');
})->name('cliente.catalogo');

// Grupo de usuario
Route::prefix('usuario')->name('usuario.')->group(function () {
    Route::get('/catalogo', function () { $error = protegerRuta(); if ($error) return $error; if (session('user_type') == 1) return redirect()->route('admin.inicio'); return view('index'); })->name('catalogo');
    Route::get('/carrito', function () { $error = protegerRuta(); if ($error) return $error; if (session('user_type') == 1) return redirect()->route('admin.inicio'); return view('Usuarios.CarritoCompras'); })->name('carrito');
    Route::get('/ayuda-contacto', function () { $error = protegerRuta(); if ($error) return $error; if (session('user_type') == 1) return redirect()->route('admin.inicio'); return view('Usuarios.AyudaContacto'); })->name('ayuda.contacto');
    Route::get('/pedidos', function () { $error = protegerRuta(); if ($error) return $error; if (session('user_type') == 1) return redirect()->route('admin.inicio'); return view('Usuarios.PedidosUsuario'); })->name('pedidos');
});

// =============================================================================
// RUTAS PARA ADMINISTRADORES (SOLO Rol 1)
// =============================================================================

// Panel de administración
Route::get('/admin/inicio', function () { 
    $error = protegerRuta(1); if ($error) return $error;
    return view('VistasAdmin.welcome');
})->name('admin.inicio');

Route::get('/admin', function () { return redirect()->route('admin.inicio'); });

// -------------------------------------------------------------------------
// CRUD CLIENTES (Admin) - CORREGIDAS
// -------------------------------------------------------------------------
Route::get('/clientes', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(ClienteController::class)->index($request); })->name('clientes.index');
Route::post('/clientes', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(ClienteController::class)->store($request); })->name('clientes.store');
Route::get('/clientes/{idCliente}', function (Request $request, $idCliente) { $error = protegerRuta(1); if ($error) return $error; return app(ClienteController::class)->edit($request, $idCliente); })->name('clientes.edit');
Route::put('/clientes/{idCliente}', function (Request $request, $idCliente) { $error = protegerRuta(1); if ($error) return $error; return app(ClienteController::class)->update($request, $idCliente); })->name('clientes.update');
Route::delete('/clientes/{idCliente}', function (Request $request, $idCliente) { $error = protegerRuta(1); if ($error) return $error; return app(ClienteController::class)->destroy($request, $idCliente); })->name('clientes.destroy');

// -------------------------------------------------------------------------
// CRUD PRODUCTOS (Admin) - CORREGIDAS
// -------------------------------------------------------------------------
Route::get('/productos', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(ProductosController::class)->index($request); })->name('productos.index');
Route::post('/productos', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(ProductosController::class)->store($request); })->name('productos.store');
Route::get('/productos/{idProducto}', function (Request $request, $idProducto) { $error = protegerRuta(1); if ($error) return $error; return app(ProductosController::class)->edit($request, $idProducto); })->name('productos.edit');
Route::put('/productos/{idProducto}', function (Request $request, $idProducto) { $error = protegerRuta(1); if ($error) return $error; return app(ProductosController::class)->update($request, $idProducto); })->name('productos.update');
Route::delete('/productos/{idProducto}', function (Request $request, $idProducto) { $error = protegerRuta(1); if ($error) return $error; return app(ProductosController::class)->destroy($request, $idProducto); })->name('productos.destroy');
Route::get('/productos/next-id', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(ProductosController::class)->getNextProductId($request); })->name('productos.next.id');

// -------------------------------------------------------------------------
// CRUD PEDIDOS (Admin) - CORREGIDAS
// -------------------------------------------------------------------------
Route::get('/pedidos', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->index($request); })->name('pedidos.index');
Route::post('/pedidos', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->store($request); })->name('pedidos.store');
Route::get('/pedidos/create', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->create($request); })->name('pedidos.create');
Route::get('/pedidos/{pedido}', function (Request $request, $pedido) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->show($request, $pedido); })->name('pedidos.show');
Route::get('/pedidos/{pedido}/edit', function (Request $request, $pedido) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->edit($request, $pedido); })->name('pedidos.edit');
Route::put('/pedidos/{pedido}', function (Request $request, $pedido) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->update($request, $pedido); })->name('pedidos.update');
Route::delete('/pedidos/{pedido}', function (Request $request, $pedido) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->destroy($request, $pedido); })->name('pedidos.destroy');
Route::get('/pedidos/{id}/cliente-info', function (Request $request, $id) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->getClienteInfo($request, $id); })->name('pedidos.cliente.info');
Route::get('/pedidos/{id}/repartidor-info', function (Request $request, $id) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->getRepartidorInfo($request, $id); })->name('pedidos.repartidor.info');
Route::get('/pedidos/{id}/calcular-valor', function (Request $request, $id) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->calcularValorPedido($request, $id); })->name('pedidos.calcular.valor');
Route::put('/pedidos/{id}/actualizar-valor', function (Request $request, $id) { $error = protegerRuta(1); if ($error) return $error; return app(PedidosController::class)->actualizarValorPedido($request, $id); })->name('pedidos.actualizar.valor');

// -------------------------------------------------------------------------
// PRODUCTOS EN PEDIDOS (Admin) - CORREGIDAS
// -------------------------------------------------------------------------
Route::get('/pedidos/{id}/productos', function (Request $request, $id) { $error = protegerRuta(1); if ($error) return $error; return app(ProductosPedidoController::class)->index($request, $id); })->name('pedidos.productos.index');
Route::post('/pedidos/{id}/productos', function (Request $request, $id) { $error = protegerRuta(1); if ($error) return $error; return app(ProductosPedidoController::class)->store($request, $id); })->name('pedidos.productos.store');
Route::delete('/pedidos/{idPedido}/productos/{idProducto}', function (Request $request, $idPedido, $idProducto) { $error = protegerRuta(1); if ($error) return $error; return app(ProductosPedidoController::class)->destroy($request, $idPedido, $idProducto); })->name('pedidos.productos.destroy');

// -------------------------------------------------------------------------
// CRUD PROVEEDORES (Admin) - CORREGIDAS
// -------------------------------------------------------------------------
Route::get('/proveedores', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(ProveedorController::class)->index($request); })->name('proveedores.index');
Route::get('/proveedores/create', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(ProveedorController::class)->create($request); })->name('proveedores.create');
Route::post('/proveedores', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(ProveedorController::class)->store($request); })->name('proveedores.store');
Route::get('/proveedores/{NITProveedores}/edit', function (Request $request, $NITProveedores) { $error = protegerRuta(1); if ($error) return $error; return app(ProveedorController::class)->edit($request, $NITProveedores); })->name('proveedores.edit');
Route::put('/proveedores/{NITProveedores}', function (Request $request, $NITProveedores) { $error = protegerRuta(1); if ($error) return $error; return app(ProveedorController::class)->update($request, $NITProveedores); })->name('proveedores.update');
Route::delete('/proveedores/{NITProveedores}', function (Request $request, $NITProveedores) { $error = protegerRuta(1); if ($error) return $error; return app(ProveedorController::class)->destroy($request, $NITProveedores); })->name('proveedores.destroy');
Route::get('/proveedores/get-siguiente-nit', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(ProveedorController::class)->getSiguienteNIT($request); })->name('proveedores.getSiguienteNIT');

// -------------------------------------------------------------------------
// CRUD ROLES (Admin) - CORREGIDAS
// -------------------------------------------------------------------------
Route::get('/roles', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(RolesController::class)->index($request); })->name('roles.index');
Route::get('/roles/create', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(RolesController::class)->create($request); })->name('roles.create');
Route::post('/roles', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(RolesController::class)->store($request); })->name('roles.store');
Route::get('/roles/{idRol}/edit', function (Request $request, $idRol) { $error = protegerRuta(1); if ($error) return $error; return app(RolesController::class)->edit($request, $idRol); })->name('roles.edit');
Route::put('/roles/{idRol}', function (Request $request, $idRol) { $error = protegerRuta(1); if ($error) return $error; return app(RolesController::class)->update($request, $idRol); })->name('roles.update');
Route::delete('/roles/{idRol}', function (Request $request, $idRol) { $error = protegerRuta(1); if ($error) return $error; return app(RolesController::class)->destroy($request, $idRol); })->name('roles.destroy');

// -------------------------------------------------------------------------
// CRUD USUARIOS (Admin) - CORREGIDAS
// -------------------------------------------------------------------------
Route::get('/usuarios', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(UsuariosController::class)->index($request); })->name('usuarios.index');
Route::post('/usuarios', function (Request $request) { $error = protegerRuta(1); if ($error) return $error; return app(UsuariosController::class)->store($request); })->name('usuarios.store');
Route::put('/usuarios/{idUsuario}', function (Request $request, $idUsuario) { $error = protegerRuta(1); if ($error) return $error; return app(UsuariosController::class)->update($request, $idUsuario); })->name('usuarios.update');
Route::delete('/usuarios/{idUsuario}', function (Request $request, $idUsuario) { $error = protegerRuta(1); if ($error) return $error; return app(UsuariosController::class)->destroy($request, $idUsuario); })->name('usuarios.destroy');
Route::put('/usuarios/{idUsuario}/cambiar-rol', function (Request $request, $idUsuario) { $error = protegerRuta(1); if ($error) return $error; return app(UsuariosController::class)->cambiarRol($request, $idUsuario); })->name('usuarios.cambiar-rol');

// -------------------------------------------------------------------------
// ASIGNACIÓN DE USUARIOS A ROLES (Admin) - CORREGIDAS
// -------------------------------------------------------------------------
Route::get('/roles/{idRol}/usuarios', function (Request $request, $idRol) { $error = protegerRuta(1); if ($error) return $error; return app(RolesController::class)->getUsuariosParaRol($request, $idRol); })->name('roles.usuarios');
Route::post('/roles/{idRol}/asignar-usuarios', function (Request $request, $idRol) { $error = protegerRuta(1); if ($error) return $error; return app(RolesController::class)->asignarUsuarios($request, $idRol); })->name('roles.asignar-usuarios');

// =============================================================================
// RUTAS COMPARTIDAS PARA TODOS LOS AUTENTICADOS
// =============================================================================

// Ruta bienvenido
Route::get('/bienvenido', function () { $error = protegerRuta(); if ($error) return $error; return view('VistasAdmin.welcome'); })->name('welcome');

// Dashboard
Route::get('/dashboard', function () { $error = protegerRuta(); if ($error) return $error; return view('dashboard'); })->name('dashboard');

// Ayuda y contacto
Route::get('/ayuda-contacto', function (Request $request) { $error = protegerRuta(); if ($error) return $error; return app(AyudaContactoController::class)->index($request); })->name('AyudaContacto.index');

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

// =============================================================================
// RUTAS API PARA AUTENTICACIÓN
// =============================================================================
Route::prefix('api')->group(function () {
    Route::get('/check-auth', [AuthController::class, 'checkAuth']);
    Route::get('/user-info', [AuthController::class, 'getUserInfo']);
});