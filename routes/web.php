<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// Controladores
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
use App\Http\Controllers\UsuariosAuthController;

// =============================================================================
// FUNCIÓN DE PROTECCIÓN REUTILIZABLE
// =============================================================================
function protegerRuta($roleRequerido = null) {
    if (!session()->has('user_id')) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión');
    }
    
    if ($roleRequerido && session('user_type') != $roleRequerido) {
        return session('user_type') == 1
            ? redirect()->route('admin.inicio')->with('error', 'Acceso restringido')
            : redirect()->route('catalogo')->with('error', 'Acceso restringido a administradores');
    }
    
    return null;
}

// =============================================================================
// RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
// =============================================================================

// Página principal
Route::get('/', function () { 
    return session()->has('user_id') 
        ? (session('user_type') == 1 ? redirect()->route('admin.inicio') : redirect()->route('catalogo'))
        : view('index');
})->name('home');

// Autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// =============================================================================
// RUTAS PARA CLIENTES/REPARTIDORES (Rol 2 o 3)
// =============================================================================

Route::get('/catalogo', function () {
    $error = protegerRuta(); 
    return $error ?: (session('user_type') == 1 ? redirect()->route('admin.inicio') : view('index'));
})->name('catalogo');

Route::prefix('usuario')->name('usuario.')->group(function () {
    Route::get('/catalogo', fn() => redirect()->route('catalogo'))->name('catalogo');
    Route::get('/carrito', fn() => (protegerRuta() ?: (session('user_type') == 1 ? redirect()->route('admin.inicio') : view('Usuarios.CarritoCompras'))))->name('carrito');
    Route::get('/ayuda-contacto', fn() => (protegerRuta() ?: (session('user_type') == 1 ? redirect()->route('admin.inicio') : view('Usuarios.AyudaContacto'))))->name('ayuda.contacto');
    Route::get('/pedidos', fn() => (protegerRuta() ?: (session('user_type') == 1 ? redirect()->route('admin.inicio') : view('Usuarios.PedidosUsuario'))))->name('pedidos');
});

// =============================================================================
// RUTAS PARA ADMINISTRADORES (SOLO Rol 1)
// =============================================================================

Route::get('/admin/inicio', fn() => (protegerRuta(1) ?: view('VistasAdmin.welcome')))->name('admin.inicio');
Route::get('/admin', fn() => redirect()->route('admin.inicio'));

// -------------------------------------------------------------------------
// GESTIÓN COMPLETA DE USUARIOS Y ROLES (NUEVO SISTEMA)
// -------------------------------------------------------------------------
Route::prefix('roles')->name('roles.')->group(function () {
    // Vista principal con pestañas
    Route::get('/', fn(Request $request) => (protegerRuta(1) ?: app(RolesController::class)->index($request)))->name('index');
    
    // CRUD de Roles (rutas normales)
    Route::post('/', fn(Request $request) => (protegerRuta(1) ?: app(RolesController::class)->store($request)))->name('store');
    Route::put('/{idRol}', fn(Request $request, $idRol) => (protegerRuta(1) ?: app(RolesController::class)->update($request, $idRol)))->name('update');
    Route::delete('/{idRol}', fn(Request $request, $idRol) => (protegerRuta(1) ?: app(RolesController::class)->destroy($idRol)))->name('destroy');
    
    // Rutas alternativas para compatibilidad
    Route::post('/store', fn(Request $request) => (protegerRuta(1) ?: app(RolesController::class)->store($request)))->name('storeRol');
    Route::put('/{idRol}/update', fn(Request $request, $idRol) => (protegerRuta(1) ?: app(RolesController::class)->update($request, $idRol)))->name('updateRol');
    Route::delete('/{idRol}/delete', fn(Request $request, $idRol) => (protegerRuta(1) ?: app(RolesController::class)->destroy($idRol)))->name('destroyRol');
    
    // CRUD de Usuarios (dentro del controlador de roles) - RUTAS NUEVAS
    Route::post('/usuarios/store', fn(Request $request) => (protegerRuta(1) ?: app(RolesController::class)->storeUsuario($request)))->name('usuarios.store');
    Route::put('/usuarios/{idUsuario}', fn(Request $request, $idUsuario) => (protegerRuta(1) ?: app(RolesController::class)->updateUsuario($request, $idUsuario)))->name('usuarios.update');
    Route::delete('/usuarios/{idUsuario}', fn(Request $request, $idUsuario) => (protegerRuta(1) ?: app(RolesController::class)->destroyUsuario($idUsuario)))->name('usuarios.destroy');
    
    // Acciones rápidas
    Route::put('/usuarios/{idUsuario}/cambiar-rol', fn(Request $request, $idUsuario) => (protegerRuta(1) ?: app(RolesController::class)->cambiarRol($request, $idUsuario)))->name('usuarios.cambiar-rol');
    Route::put('/usuarios/{idUsuario}/toggle-estado', fn(Request $request, $idUsuario) => (protegerRuta(1) ?: app(RolesController::class)->toggleEstado($idUsuario)))->name('usuarios.toggleEstado');
    
    // Detalles de usuario
    Route::get('/usuarios/{idUsuario}', fn(Request $request, $idUsuario) => (protegerRuta(1) ?: app(RolesController::class)->verUsuario($idUsuario)))->name('usuarios.show');
    
    // Rutas antiguas para compatibilidad (mantener por si acaso)
    Route::post('/usuarios', fn(Request $request) => (protegerRuta(1) ?: app(RolesController::class)->storeUsuario($request)))->name('usuarios.store.alternative');
    Route::put('/usuarios/{idUsuario}/update', fn(Request $request, $idUsuario) => (protegerRuta(1) ?: app(RolesController::class)->updateUsuario($request, $idUsuario)))->name('usuarios.update.alternative');
});

// -------------------------------------------------------------------------
// AUTENTICACIÓN DE USUARIOS (módulo existente)
// -------------------------------------------------------------------------
Route::prefix('admin/usuarios-auth')->name('usuarios.auth.')->group(function () {
    Route::get('/', [UsuariosAuthController::class, 'index'])->name('index');
    Route::post('/{idUsuario}/verificar', [UsuariosAuthController::class, 'verificarPassword'])->name('verificar');
    Route::post('/generar-hash', [UsuariosAuthController::class, 'generarHash'])->name('generar.hash');
    Route::post('/verificar-global', [UsuariosAuthController::class, 'verificarGlobal'])->name('verificar.global');
});

// -------------------------------------------------------------------------
// CRUD PEDIDOS (existente - mantener)
// -------------------------------------------------------------------------
Route::prefix('pedidos')->name('pedidos.')->group(function () {
    Route::get('/', fn(Request $request) => (protegerRuta(1) ?: app(PedidosController::class)->index($request)))->name('index');
    Route::post('/', fn(Request $request) => (protegerRuta(1) ?: app(PedidosController::class)->store($request)))->name('store');
    Route::get('/create', fn(Request $request) => (protegerRuta(1) ?: app(PedidosController::class)->create($request)))->name('create');
    Route::get('/{pedido}', fn(Request $request, $pedido) => (protegerRuta(1) ?: app(PedidosController::class)->show($request, $pedido)))->name('show');
    Route::get('/{pedido}/edit', fn(Request $request, $pedido) => (protegerRuta(1) ?: app(PedidosController::class)->edit($request, $pedido)))->name('edit');
    Route::put('/{pedido}', fn(Request $request, $pedido) => (protegerRuta(1) ?: app(PedidosController::class)->update($request, $pedido)))->name('update');
    Route::delete('/{pedido}', fn(Request $request, $pedido) => (protegerRuta(1) ?: app(PedidosController::class)->destroy($request, $pedido)))->name('destroy');
    
    Route::prefix('{idPedido}/productos')->name('productos.')->group(function () {
        Route::get('/', fn(Request $request, $idPedido) => (protegerRuta(1) ?: app(ProductosPedidoController::class)->index($idPedido)))->name('index');
        Route::post('/', fn(Request $request, $idPedido) => (protegerRuta(1) ?: app(ProductosPedidoController::class)->store($request, $idPedido)))->name('store');
        Route::get('/create', fn(Request $request, $idPedido) => (protegerRuta(1) ?: app(ProductosPedidoController::class)->create($idPedido)))->name('create');
        Route::get('/{idProducto}', fn(Request $request, $idPedido, $idProducto) => (protegerRuta(1) ?: app(ProductosPedidoController::class)->show($idPedido, $idProducto)))->name('show');
        Route::get('/{idProducto}/edit', fn(Request $request, $idPedido, $idProducto) => (protegerRuta(1) ?: app(ProductosPedidoController::class)->edit($idPedido, $idProducto)))->name('edit');
        Route::put('/{idProducto}', fn(Request $request, $idPedido, $idProducto) => (protegerRuta(1) ?: app(ProductosPedidoController::class)->update($request, $idPedido, $idProducto)))->name('update');
        Route::delete('/{idProducto}', fn(Request $request, $idPedido, $idProducto) => (protegerRuta(1) ?: app(ProductosPedidoController::class)->destroy($idPedido, $idProducto)))->name('destroy');
    });
});

// -------------------------------------------------------------------------
// CRUD CLIENTES (mantener para compatibilidad)
// -------------------------------------------------------------------------
Route::get('/clientes', fn(Request $request) => (protegerRuta(1) ?: app(ClienteController::class)->index($request)))->name('clientes.index');
Route::post('/clientes', fn(Request $request) => (protegerRuta(1) ?: app(ClienteController::class)->store($request)))->name('clientes.store');
Route::get('/clientes/{idCliente}', fn(Request $request, $idCliente) => (protegerRuta(1) ?: app(ClienteController::class)->edit($request, $idCliente)))->name('clientes.edit');
Route::put('/clientes/{idCliente}', fn(Request $request, $idCliente) => (protegerRuta(1) ?: app(ClienteController::class)->update($request, $idCliente)))->name('clientes.update');
Route::delete('/clientes/{idCliente}', fn(Request $request, $idCliente) => (protegerRuta(1) ?: app(ClienteController::class)->destroy($request, $idCliente)))->name('clientes.destroy');

// -------------------------------------------------------------------------
// CRUD PRODUCTOS (existente - mantener)
// -------------------------------------------------------------------------
Route::get('/productos', fn(Request $request) => (protegerRuta(1) ?: app(ProductosController::class)->index($request)))->name('productos.index');
Route::post('/productos', fn(Request $request) => (protegerRuta(1) ?: app(ProductosController::class)->store($request)))->name('productos.store');
Route::get('/productos/{idProducto}', fn(Request $request, $idProducto) => (protegerRuta(1) ?: app(ProductosController::class)->edit($request, $idProducto)))->name('productos.edit');
Route::put('/productos/{idProducto}', fn(Request $request, $idProducto) => (protegerRuta(1) ?: app(ProductosController::class)->update($request, $idProducto)))->name('productos.update');
Route::delete('/productos/{idProducto}', fn(Request $request, $idProducto) => (protegerRuta(1) ?: app(ProductosController::class)->destroy($request, $idProducto)))->name('productos.destroy');
Route::get('/productos/next-id', fn(Request $request) => (protegerRuta(1) ?: app(ProductosController::class)->getNextProductId($request)))->name('productos.next.id');

// -------------------------------------------------------------------------
// CRUD PROVEEDORES (existente - mantener)
// -------------------------------------------------------------------------
Route::get('/proveedores', fn(Request $request) => (protegerRuta(1) ?: app(ProveedorController::class)->index($request)))->name('proveedores.index');
Route::post('/proveedores', fn(Request $request) => (protegerRuta(1) ?: app(ProveedorController::class)->store($request)))->name('proveedores.store');
Route::get('/proveedores/create', fn(Request $request) => (protegerRuta(1) ?: app(ProveedorController::class)->create($request)))->name('proveedores.create');
Route::get('/proveedores/{proveedor}', fn(Request $request, $proveedor) => (protegerRuta(1) ?: app(ProveedorController::class)->show($request, $proveedor)))->name('proveedores.show');
Route::get('/proveedores/{proveedor}/edit', fn(Request $request, $proveedor) => (protegerRuta(1) ?: app(ProveedorController::class)->edit($request, $proveedor)))->name('proveedores.edit');
Route::put('/proveedores/{proveedor}', fn(Request $request, $proveedor) => (protegerRuta(1) ?: app(ProveedorController::class)->update($request, $proveedor)))->name('proveedores.update');
Route::delete('/proveedores/{proveedor}', fn(Request $request, $proveedor) => (protegerRuta(1) ?: app(ProveedorController::class)->destroy($request, $proveedor)))->name('proveedores.destroy');
Route::get('/proveedores/siguiente-nit', fn(Request $request) => (protegerRuta(1) ?: app(ProveedorController::class)->getSiguienteNIT($request)))->name('proveedores.getSiguienteNIT');

// -------------------------------------------------------------------------
// CRUD USUARIOS (rutas alternativas - mantener compatibilidad)
// -------------------------------------------------------------------------
Route::get('/usuarios', fn(Request $request) => (protegerRuta(1) ?: app(UsuariosController::class)->index($request)))->name('usuarios.index');
Route::post('/usuarios', fn(Request $request) => (protegerRuta(1) ?: app(UsuariosController::class)->store($request)))->name('usuarios.store');
Route::get('/usuarios/{usuario}', fn(Request $request, $usuario) => (protegerRuta(1) ?: app(UsuariosController::class)->show($request, $usuario)))->name('usuarios.show');
Route::get('/usuarios/{usuario}/edit', fn(Request $request, $usuario) => (protegerRuta(1) ?: app(UsuariosController::class)->edit($request, $usuario)))->name('usuarios.edit');
Route::put('/usuarios/{usuario}', fn(Request $request, $usuario) => (protegerRuta(1) ?: app(UsuariosController::class)->update($request, $usuario)))->name('usuarios.update');
Route::delete('/usuarios/{usuario}', fn(Request $request, $usuario) => (protegerRuta(1) ?: app(UsuariosController::class)->destroy($request, $usuario)))->name('usuarios.destroy');

// -------------------------------------------------------------------------
// CRUD AYUDA Y CONTACTO (existente - mantener)
// -------------------------------------------------------------------------
Route::get('/ayuda-contacto', fn(Request $request) => (protegerRuta(1) ?: app(AyudaContactoController::class)->index($request)))->name('AyudaContacto.index');
Route::post('/ayuda-contacto', fn(Request $request) => (protegerRuta(1) ?: app(AyudaContactoController::class)->store($request)))->name('AyudaContacto.store');
Route::get('/ayuda-contacto/{ayudaContacto}', fn(Request $request, $ayudaContacto) => (protegerRuta(1) ?: app(AyudaContactoController::class)->show($request, $ayudaContacto)))->name('AyudaContacto.show');
Route::get('/ayuda-contacto/{ayudaContacto}/edit', fn(Request $request, $ayudaContacto) => (protegerRuta(1) ?: app(AyudaContactoController::class)->edit($request, $ayudaContacto)))->name('AyudaContacto.edit');
Route::put('/ayuda-contacto/{ayudaContacto}', fn(Request $request, $ayudaContacto) => (protegerRuta(1) ?: app(AyudaContactoController::class)->update($request, $ayudaContacto)))->name('AyudaContacto.update');
Route::delete('/ayuda-contacto/{ayudaContacto}', fn(Request $request, $ayudaContacto) => (protegerRuta(1) ?: app(AyudaContactoController::class)->destroy($request, $ayudaContacto)))->name('AyudaContacto.destroy');

// =============================================================================
// RUTAS COMPARTIDAS PARA TODOS LOS AUTENTICADOS
// =============================================================================

Route::get('/bienvenido', fn() => (protegerRuta() ?: view('VistasAdmin.welcome')))->name('welcome');
Route::get('/dashboard', fn() => (protegerRuta() ?: view('dashboard')))->name('dashboard');

// =============================================================================
// RUTAS DE AUTENTICACIÓN
// =============================================================================

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout-get', function() {
    return session()->has('user_id') 
        ? (session()->flush() && redirect('/')->with('success', "Sesión cerrada. ¡Adiós " . session('user_name') . "!"))
        : redirect('/')->with('info', 'No había sesión activa');
})->name('logout.get');

Route::get('/limpiar-sesion', function() {
    session()->flush();
    return redirect('/')->with('success', 'Sesión limpiada correctamente');
});

// =============================================================================
// RUTAS DE DIAGNÓSTICO (para desarrollo)
// =============================================================================

Route::get('/test-hash', function() {
    $password = 'password123';
    $hash = Hash::make($password);
    $tuHash = '$2y$10$83LTYMwRRvZk83pMQlpBWOi9SEzogngHWJ23EDAgNRd..';
    $user = DB::table('usuarios')->where('email', 'sebbxsbt911@gmail.com')->first();
    
    echo "Password: $password<br>";
    echo "Hash generado: $hash<br><br>";
    echo "Tu hash: $tuHash<br>";
    echo "Verificación: " . (Hash::check($password, $tuHash) ? 'VERDADERO' : 'FALSO') . "<br><br>";
    
    if ($user) {
        echo "Usuario encontrado:<br>";
        echo "ID: {$user->idUsuario}<br>";
        echo "Email: {$user->email}<br>";
        echo "Hash en BD: {$user->password}<br>";
        echo "Verificación con BD: " . (Hash::check($password, $user->password) ? 'VERDADERO' : 'FALSO') . "<br>";
    } else {
        echo "Usuario no encontrado";
    }
});

Route::get('/create-test-user', function() {
    $user = \App\Models\Usuario::create([
        'nombre' => 'Usuario Test',
        'email' => 'test@test.com',
        'password' => Hash::make('test123'),
        'idRol' => 2
    ]);
    echo "Usuario creado con ID: {$user->idUsuario}";
});

Route::get('/debug-routes', function() {
    echo '<h3>Rutas disponibles:</h3><pre>';
    print_r(collect(Route::getRoutes())->map(fn($route) => [
        'method' => implode('|', $route->methods()),
        'uri' => $route->uri(),
        'name' => $route->getName(),
        'action' => $route->getActionName(),
    ])->toArray());
    echo '</pre>';
});

// =============================================================================
// RUTAS DE FALLBACK (404)
// =============================================================================

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});