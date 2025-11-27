<?php

// -----------------------------------------------------------------------------
// ARCHIVO COMPLETO COMBINADO CON TODAS LAS RUTAS Y TODOS LOS COMENTARIOS ORIGINALES
// -----------------------------------------------------------------------------


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductosController; 
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuariosController; 

//Agregar y mofificar en ClienteController.php para mandar a vista de inicio clientes
// Route::get('/auth/login', [LoginController::class, 'show'])->name('login');


// -----------------------------------------------------------------------------
// RUTAS PRINCIPALES DEL SISTEMA (ANTIGUAS DEL PROYECTO)
// -----------------------------------------------------------------------------


//---
//Route::get('/', function () {
//    return view('welcome'); 
//});
//----


// -----------------------------------------------------------------------------
// RUTAS CRUD CLIENTES
// -----------------------------------------------------------------------------
Route::get('/clientes', [ClienteController::class,"index"])->name('clientes.index');
Route::post('/clientes', [ClienteController::class,"store"])->name('clientes.store');
Route::get('/clientes/{idCliente}', [ClienteController::class,"edit"])->name('clientes.edit');
Route::put('/clientes/{idCliente}', [ClienteController::class,"update"])->name('clientes.update');
Route::delete('/clientes/{idCliente}', [ClienteController::class,"destroy"])->name('clientes.destroy');


// -----------------------------------------------------------------------------
// RUTAS CRUD PRODUCTOS
// -----------------------------------------------------------------------------
Route::get('/productos', [ProductosController::class,"index"])->name('productos.index');
Route::post('/productos', [ProductosController::class,"store"])->name('productos.store');
Route::get('/productos/{idProducto}', [ProductosController::class,"edit"])->name('productos.edit');
Route::put('/productos/{idProducto}', [ProductosController::class,"update"])->name('productos.update');
Route::delete('/productos/{idProducto}', [ProductosController::class, 'destroy'])->name('productos.destroy');


// -----------------------------------------------------------------------------
// RUTAS CRUD PEDIDOS
// -----------------------------------------------------------------------------
Route::get('/pedidos', [PedidosController::class,"index"])->name('pedidos.index');
Route::post('/pedidos', [PedidosController::class,"store"])->name('pedidos.store');
Route::get('/pedidos/{idPedidos}', [PedidosController::class,"edit"])->name('pedidos.edit');
Route::put('/pedidos/{idPedidos}', [PedidosController::class,"update"])->name('pedidos.update');
Route::delete('/pedidos/{idPedidos}', [PedidosController::class,"destroy"])->name('pedidos.destroy');


// -----------------------------------------------------------------------------
// RUTAS CRUD PROVEEDORES
// -----------------------------------------------------------------------------
Route::get('/proveedores', [ProveedorController::class,"index"])->name('proveedores.index');
Route::post('/proveedores', [ProveedorController::class,"store"])->name('proveedores.store');
Route::get('/proveedores/{idProveedor}', [ProveedorController::class,"edit"])->name('proveedores.edit');
Route::put('/proveedores/{idProveedor}', [ProveedorController::class,"update"])->name('proveedores.update');
Route::delete('/proveedores/{idProveedor}', [ProveedorController::class,"destroy"])->name('proveedores.destroy');
// -----------------------------------------------------------------------------
// RUTAS CRUD ROLES
// -----------------------------------------------------------------------------
Route::get('/roles', [App\Http\Controllers\RolesController::class, 'index'])->name('roles.index');
Route::get('/roles/create', [App\Http\Controllers\RolesController::class, 'create'])->name('roles.create');
Route::post('/roles', [App\Http\Controllers\RolesController::class, 'store'])->name('roles.store');
Route::get('/roles/{idRol}/edit', [App\Http\Controllers\RolesController::class, 'edit'])->name('roles.edit');
Route::put('/roles/{idRol}', [App\Http\Controllers\RolesController::class, 'update'])->name('roles.update');
Route::delete('/roles/{idRol}', [App\Http\Controllers\RolesController::class, 'destroy'])->name('roles.destroy');
Route::put('/usuarios/{idUsuario}/cambiar-rol', [App\Http\Controllers\UsuariosController::class, 'cambiarRol'])->name('usuarios.cambiar-rol');


// NUEVAS RUTAS PARA ASIGNAR USUARIOS
Route::get('/roles/{idRol}/usuarios', [App\Http\Controllers\RolesController::class, 'getUsuariosParaRol'])->name('roles.usuarios');
Route::post('/roles/{idRol}/asignar-usuarios', [App\Http\Controllers\RolesController::class, 'asignarUsuarios'])->name('roles.asignar-usuarios');
// -----------------------------------------------------------------------------
// RUTAS CRUD USUARIOS 
Route::get('/usuarios', [App\Http\Controllers\UsuariosController::class, 'index'])->name('usuarios.index');
Route::post('/usuarios', [App\Http\Controllers\UsuariosController::class, 'store'])->name('usuarios.store');
Route::put('/usuarios/{idUsuario}', [App\Http\Controllers\UsuariosController::class, 'update'])->name('usuarios.update');
Route::delete('/usuarios/{idUsuario}', [App\Http\Controllers\UsuariosController::class, 'destroy'])->name('usuarios.destroy');

// -----------------------------------------------------------------------------
// RUTAS DE AUTENTICACIÓN Y REGISTRO (TU PARTE DEL PROYECTO)
// -----------------------------------------------------------------------------

Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/auth/logout', [AuthController::class, 'logout'])->name('logout');


// Rutas de prueba/redirección
Route::get('/dashboard', function () {
    return view('dashboard'); // crea esta vista simple o cambia a la que uses
})->name('dashboard');

// Ruta admin de ejemplo (redirige al panel admin)
Route::get('/admin', function () {
    return redirect()->route('admin.inicio');
})->middleware([]);


Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

//Nuevos agregados por nicolas


// -----------------------------------------------------------------------------
// GRUPO DE VISTAS PARA USUARIOS (CATÁLOGO, CARRITO, ETC.)
// -----------------------------------------------------------------------------
Route::prefix('usuario')->name('usuario.')->group(function () {
    
    // Catálogo principal
    Route::get('/catalogo', function () {
        return view('index');
    })->name('catalogo');

    // Carrito de compras
    Route::get('/carrito', function () {
        return view('Usuarios.CarritoCompras');
    })->name('carrito');

    // Ayuda y contacto
    Route::get('/ayuda-contacto', function () {
        return view('Usuarios.AyudaContacto');
    })->name('ayuda.contacto');

    // Pedidos usuario (página bloqueada para invitados)
    Route::get('/pedidos', function () {
        return view('Usuarios.PedidosUsuario');
    })->name('pedidos');

});


// -----------------------------------------------------------------------------
// GRUPO PARA AUTENTICACIÓN
// -----------------------------------------------------------------------------
Route::prefix('auth')->name('auth.')->group(function () {
    
    // Login
    Route::get('/login', function () {
        return view('Usuarios.IniciarSesion');
    })->name('login');

    // Registro
    Route::get('/registro', function () {
        return view('Usuarios.registro');
    })->name('register');
    
    // Registro - almacenar nuevo usuario
    Route::post('/registro', [App\Http\Controllers\RegisterController::class, 'store'])
        ->name('register.store');

    // Olvidó contraseña
    Route::get('/olvido-contraseña', function () {
        return view('Usuarios.olvidoContraseña');
    })->name('password.request');

});


// -----------------------------------------------------------------------------
// RUTA BIENVENIDO
// -----------------------------------------------------------------------------
Route::get('/bienvenido', function () {
    return view('welcome');
})->name('welcome');


// -----------------------------------------------------------------------------
// RUTA POR DEFECTO (CATÁLOGO COMO PRINCIPAL)
// -----------------------------------------------------------------------------
Route::get('/', function () {
    return view('index');
});


// -----------------------------------------------------------------------------
// RUTAS PARA ROLES
// -----------------------------------------------------------------------------

// Cliente (role 1)
Route::get('/cliente', function () {
    return view('Clientes.CatalogoU'); 
})->middleware(['auth', 'role:1']);

// Repartidor (role 2)
//Route::middleware(['role:2'])->group(function () {
//    Route::get('/resources/views/Clientes', [RepartidorController::class, 'index'])
//        ->name('repartidor.pedidos');
//});

// Administrador (role 3)
//Route::middleware(['role:3'])->group(function () {
//    Route::get('/admin/panel', [AdminController::class, 'index'])
//        ->name('admin.panel');  
//});


// Debes tener una ruta llamada 'admin.inicio' que muestre la vista admin:
Route::get('/admin/inicio', function () {
    return view('Admin.Usuarios');
})->name('admin.inicio');


// -----------------------------------------------------------------------------
// FIN DEL ARCHIVO COMPLETO Y COMBINADO
// -----------------------------------------------------------------------------

