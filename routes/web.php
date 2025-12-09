<?php

use Illuminate\Support\Facades\Route;
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
Route::get('/', function () { return view('index'); });

// -----------------------------------------------------------------------------
// AUTENTICACIÓN (Público)
// -----------------------------------------------------------------------------
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// =============================================================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
// =============================================================================

// -----------------------------------------------------------------------------
// GRUPO PARA USUARIOS REGISTRADOS
// -----------------------------------------------------------------------------
Route::middleware(['auth'])->group(function () {
    
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Ruta bienvenido
    Route::get('/bienvenido', function () { return view('VistasAdmin.welcome'); })->name('welcome');
    
    // Dashboard
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    
    // -------------------------------------------------------------------------
    // RUTAS ESPECÍFICAS DE CLIENTE
    // -------------------------------------------------------------------------
    Route::get('/cliente/catalogo', function () {
        if (session('user_type') == 'cliente') { return view('Clientes.CatalogoU'); }
        return redirect()->route('login');
    })->name('cliente.catalogo');
    
    // -------------------------------------------------------------------------
    // GRUPO DE USUARIO (Cliente/Repartidor)
    // -------------------------------------------------------------------------
    Route::prefix('usuario')->name('usuario.')->group(function () {
        Route::get('/catalogo', function () { return view('index'); })->name('catalogo');
        Route::get('/carrito', function () { return view('Usuarios.CarritoCompras'); })->name('carrito');
        Route::get('/ayuda-contacto', function () { return view('Usuarios.AyudaContacto'); })->name('ayuda.contacto');
        Route::get('/pedidos', function () { return view('Usuarios.PedidosUsuario'); })->name('pedidos');
    });
    
    // Ayuda y contacto
    Route::get('/ayuda-contacto', [AyudaContactoController::class, 'index'])->name('AyudaContacto.index');
});

// =============================================================================
// RUTAS DE ADMINISTRADOR (ADMIN - PERFIL ADMIN)
// =============================================================================

// -----------------------------------------------------------------------------
// RUTA PRINCIPAL ADMIN
// -----------------------------------------------------------------------------
Route::get('/admin/inicio', function () { return view('Admin.Usuarios'); })->name('admin.inicio');
Route::get('/admin', function () { return redirect()->route('admin.inicio'); });

// -----------------------------------------------------------------------------
// CRUD CLIENTES (Admin)
// -----------------------------------------------------------------------------
Route::get('/clientes', [ClienteController::class, "index"])->name('clientes.index');
Route::post('/clientes', [ClienteController::class, "store"])->name('clientes.store');
Route::get('/clientes/{idCliente}', [ClienteController::class, "edit"])->name('clientes.edit');
Route::put('/clientes/{idCliente}', [ClienteController::class, "update"])->name('clientes.update');
Route::delete('/clientes/{idCliente}', [ClienteController::class, "destroy"])->name('clientes.destroy');

// -----------------------------------------------------------------------------
// CRUD PRODUCTOS (Admin)
// -----------------------------------------------------------------------------
Route::get('/productos', [ProductosController::class, "index"])->name('productos.index');
Route::post('/productos', [ProductosController::class, "store"])->name('productos.store');
Route::get('/productos/{idProducto}', [ProductosController::class, "edit"])->name('productos.edit');
Route::put('/productos/{idProducto}', [ProductosController::class, "update"])->name('productos.update');
Route::delete('/productos/{idProducto}', [ProductosController::class, 'destroy'])->name('productos.destroy');
Route::get('/productos/next-id', [ProductosController::class, 'getNextProductId'])->name('productos.next.id');

// -----------------------------------------------------------------------------
// CRUD PEDIDOS (Admin)
// -----------------------------------------------------------------------------
Route::resource('pedidos', PedidosController::class);
Route::get('/pedidos/{id}/cliente-info', [PedidosController::class, 'getClienteInfo'])->name('pedidos.cliente.info');
Route::get('/pedidos/{id}/repartidor-info', [PedidosController::class, 'getRepartidorInfo'])->name('pedidos.repartidor.info');
Route::get('/pedidos/{id}/calcular-valor', [PedidosController::class, 'calcularValorPedido'])->name('pedidos.calcular.valor');
Route::put('/pedidos/{id}/actualizar-valor', [PedidosController::class, 'actualizarValorPedido'])->name('pedidos.actualizar.valor');

// -----------------------------------------------------------------------------
// PRODUCTOS EN PEDIDOS (Admin)
// -----------------------------------------------------------------------------
Route::get('/pedidos/{id}/productos', [ProductosPedidoController::class, 'index'])->name('pedidos.productos.index');
Route::post('/pedidos/{id}/productos', [ProductosPedidoController::class, 'store'])->name('pedidos.productos.store');
Route::delete('/pedidos/{idPedido}/productos/{idProducto}', [ProductosPedidoController::class, 'destroy'])->name('pedidos.productos.destroy');

// -----------------------------------------------------------------------------
// CRUD PROVEEDORES (Admin)
// -----------------------------------------------------------------------------
Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
Route::get('/proveedores/create', [ProveedorController::class, 'create'])->name('proveedores.create');
Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
Route::get('/proveedores/{NITProveedores}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
Route::put('/proveedores/{NITProveedores}', [ProveedorController::class, 'update'])->name('proveedores.update');
Route::delete('/proveedores/{NITProveedores}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
Route::get('/proveedores/get-siguiente-nit', [ProveedorController::class, 'getSiguienteNIT'])->name('proveedores.getSiguienteNIT');

// -----------------------------------------------------------------------------
// CRUD ROLES (Admin)
// -----------------------------------------------------------------------------
Route::get('/roles', [RolesController::class, 'index'])->name('roles.index');
Route::get('/roles/create', [RolesController::class, 'create'])->name('roles.create');
Route::post('/roles', [RolesController::class, 'store'])->name('roles.store');
Route::get('/roles/{idRol}/edit', [RolesController::class, 'edit'])->name('roles.edit');
Route::put('/roles/{idRol}', [RolesController::class, 'update'])->name('roles.update');
Route::delete('/roles/{idRol}', [RolesController::class, 'destroy'])->name('roles.destroy');

// -----------------------------------------------------------------------------
// CRUD USUARIOS (Admin)
// -----------------------------------------------------------------------------
Route::get('/usuarios', [UsuariosController::class, 'index'])->name('usuarios.index');
Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
Route::put('/usuarios/{idUsuario}', [UsuariosController::class, 'update'])->name('usuarios.update');
Route::delete('/usuarios/{idUsuario}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
Route::put('/usuarios/{idUsuario}/cambiar-rol', [UsuariosController::class, 'cambiarRol'])->name('usuarios.cambiar-rol');

// -----------------------------------------------------------------------------
// ASIGNACIÓN DE USUARIOS A ROLES (Admin)
// -----------------------------------------------------------------------------
Route::get('/roles/{idRol}/usuarios', [RolesController::class, 'getUsuariosParaRol'])->name('roles.usuarios');
Route::post('/roles/{idRol}/asignar-usuarios', [RolesController::class, 'asignarUsuarios'])->name('roles.asignar-usuarios');