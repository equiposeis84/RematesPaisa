<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductosController; 
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuariosController; 

//Agregar y mofificar en ClienteController.php para mandar a vista de inicio clientes
// Route::get('/auth/login', [LoginController::class, 'show'])->name('login');
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


Route::get('/clientes', [ClienteController::class,"index"])->name('clientes.index');
Route::post('/clientes', [ClienteController::class,"store"])->name('clientes.store');
Route::get('/clientes/{idCliente}', [ClienteController::class,"edit"])->name('clientes.edit');
Route::put('/clientes/{idCliente}', [ClienteController::class,"update"])->name('clientes.update');
Route::delete('/clientes/{idCliente}', [ClienteController::class,"destroy"])->name('clientes.destroy');


Route::resource('proveedores', ProveedorController::class);
Route::resource('productos', ProductosController::class);



// Grupo para vistas públicas de usuarios
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

// Grupo para autenticación
Route::prefix('auth')->name('auth.')->group(function () {
    
    // Login
    Route::get('/login', function () {
        return view('Usuarios.IniciarSesion');
    })->name('login');

    // Registro
    Route::get('/registro', function () {return view('Usuarios.registro');})->name('register');
    
    // Registro - almacenar nuevo usuario
    Route::post('/registro', [App\Http\Controllers\RegistroController::class, 'store'])
    ->name('register.store');

    // Olvidó contraseña
    Route::get('/olvido-contraseña', function () {
        return view('Usuarios.olvidoContraseña');
    })->name('password.request');

});

// Ruta de bienvenida (si existe)
Route::get('/bienvenido', function () {
    return view('welcome');
})->name('welcome');

// Ruta por defecto (catálogo como página principal)
Route::get('/', function () {
    return view('index');
});

<<<<<<< HEAD
//Rutas para roles
=======

// -----------------------------------------------------------------------------
// RUTAS PARA ROLES
// -----------------------------------------------------------------------------

// Cliente (role 2)
>>>>>>> 4d5b91948a2ec94d35c21f5a3453a124164b06c6
Route::get('/cliente', function () {
    return view('Clientes.CatalogoU'); 
})->middleware(['auth', 'role:1']);

<<<<<<< HEAD
Route::middleware(['role:2'])->group(function () {
    Route::get('/resources/views/Clientes', [RepartidorController::class, 'index'])->name('repartidor.pedidos');
    // vistas de REPARTIDOR
});
Route::middleware(['role:3'])->group(function () {
    Route::get('/admin/panel', [AdminController::class, 'index'])
        ->name('admin.panel');   // Vista del administrador
});
//Fin nuevos agregados por nicolas
=======
// Repartidor (role 3)
//Route::middleware(['role:3'])->group(function () {
//    Route::get('/resources/views/Clientes', [RepartidorController::class, 'index'])
//        ->name('repartidor.pedidos');
//});

// Administrador (role 1)
//Route::middleware(['role:1'])->group(function () {
//    Route::get('/admin/panel', [AdminController::class, 'index'])
//        ->name('admin.panel');  
//});
>>>>>>> 4d5b91948a2ec94d35c21f5a3453a124164b06c6


// Debes tener una ruta llamada 'admin.inicio' que muestre la vista admin:
Route::get('/admin/inicio', function () {
    // si tu vista admin se llama Admin.Usuarios:
    return view('Admin.Usuarios');
})->name('admin.inicio');
