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
use App\Http\Controllers\CatalogoController;

// =============================================================================
// FUNCIÓN PARA VERIFICAR ADMIN
// =============================================================================
function verificarAdmin() {
    if (!session()->has('user_id')) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión');
    }
    
    if (session('user_type') != 1) {
        return redirect()->route('catalogo')->with('error', 'Acceso restringido a administradores');
    }
    
    return null;
}

// =============================================================================
// RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
// =============================================================================

// Página principal
Route::get('/', function () { 
    // Si ya está logueado, redirigir según su rol
    if (session()->has('user_id')) {
        return session('user_type') == 1 
            ? redirect()->route('admin.inicio') 
            : redirect()->route('catalogo');
    }
    // Si no está logueado, mostrar vista pública
    return view('index');
})->name('home');

// Autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// =============================================================================
// RUTAS PÚBLICAS DEL CATÁLOGO (ACCESO SIN REGISTRO)
// =============================================================================

// Ruta principal del catálogo - PÚBLICA
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');

// Ruta para ver detalles de producto - PÚBLICA
Route::get('/catalogo/producto/{id}', [CatalogoController::class, 'show'])->name('catalogo.show');

// Rutas del carrito (públicas)
Route::post('/catalogo/carrito/agregar', [CatalogoController::class, 'addToCart'])->name('catalogo.add');
Route::get('/catalogo/carrito', [CatalogoController::class, 'getCart'])->name('catalogo.cart.get');
Route::post('/catalogo/carrito/actualizar', [CatalogoController::class, 'updateCart'])->name('catalogo.cart.update');
Route::post('/catalogo/carrito/eliminar', [CatalogoController::class, 'removeFromCart'])->name('catalogo.cart.remove');
Route::post('/catalogo/carrito/vaciar', [CatalogoController::class, 'clearCart'])->name('catalogo.cart.clear');

// =============================================================================
// RUTAS PARA USUARIOS REGISTRADOS Y PÚBLICAS
// =============================================================================

Route::prefix('usuario')->name('usuario.')->group(function () {
    // Redirección al catálogo público
    Route::get('/catalogo', fn() => redirect()->route('catalogo'))->name('catalogo');
    
    // Carrito (accesible tanto para usuarios como invitados)
    Route::get('/carrito', function () {
        // Solo verificar si es admin
        if (session()->has('user_id') && session('user_type') == 1) {
            return redirect()->route('admin.inicio')->with('error', 'Acceso restringido');
        }
        return view('catalogo')->with('activeTab', 'carrito');
    })->name('carrito');
    
    // Ayuda y contacto (accesible para todos)
    Route::get('/ayuda-contacto', function () {
        // Solo verificar si es admin
        if (session()->has('user_id') && session('user_type') == 1) {
            return redirect()->route('admin.inicio')->with('error', 'Acceso restringido');
        }
        return view('catalogo')->with('activeTab', 'ayuda');
    })->name('ayuda.contacto');
    
    // Pedidos (SOLO para usuarios registrados)
    Route::get('/pedidos', function () {
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus pedidos');
        }
        
        if (session('user_type') == 1) {
            return redirect()->route('admin.inicio')->with('error', 'Acceso restringido');
        }
        
        return view('catalogo')->with('activeTab', 'pedidos');
    })->name('pedidos');
});

// =============================================================================
// RUTAS PARA ADMINISTRADORES (SOLO Rol 1)
// =============================================================================

// Grupo de rutas protegidas para admin
Route::get('/admin/inicio', function () {
    $error = verificarAdmin();
    if ($error) return $error;
    return view('VistasAdmin.welcome');
})->name('admin.inicio');

Route::get('/admin', function () {
    $error = verificarAdmin();
    if ($error) return $error;
    return redirect()->route('admin.inicio');
});

// -------------------------------------------------------------------------
// GESTIÓN COMPLETA DE USUARIOS Y ROLES (SOLO ADMIN)
// -------------------------------------------------------------------------
Route::prefix('roles')->name('roles.')->group(function () {
    // Ruta index con verificación manual
    Route::get('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->index($request);
    })->name('index');
    
    // CRUD de Roles
    Route::post('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->store($request);
    })->name('store');
    
    Route::put('/{idRol}', function (Request $request, $idRol) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->update($request, $idRol);
    })->name('update');
    
    Route::delete('/{idRol}', function (Request $request, $idRol) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->destroy($idRol);
    })->name('destroy');
    
    // Rutas alternativas para compatibilidad
    Route::post('/store', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->store($request);
    })->name('storeRol');
    
    Route::put('/{idRol}/update', function (Request $request, $idRol) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->update($request, $idRol);
    })->name('updateRol');
    
    Route::delete('/{idRol}/delete', function (Request $request, $idRol) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->destroy($idRol);
    })->name('destroyRol');
    
    // CRUD de Usuarios
    Route::post('/usuarios/store', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->storeUsuario($request);
    })->name('usuarios.store');
    
    Route::put('/usuarios/{idUsuario}', function (Request $request, $idUsuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->updateUsuario($request, $idUsuario);
    })->name('usuarios.update');
    
    Route::delete('/usuarios/{idUsuario}', function (Request $request, $idUsuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->destroyUsuario($idUsuario);
    })->name('usuarios.destroy');
    
    // Acciones rápidas
    Route::put('/usuarios/{idUsuario}/cambiar-rol', function (Request $request, $idUsuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->cambiarRol($request, $idUsuario);
    })->name('usuarios.cambiar-rol');
    
    Route::put('/usuarios/{idUsuario}/toggle-estado', function (Request $request, $idUsuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->toggleEstado($idUsuario);
    })->name('usuarios.toggleEstado');
    
    // Detalles de usuario
    Route::get('/usuarios/{idUsuario}', function (Request $request, $idUsuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->verUsuario($idUsuario);
    })->name('usuarios.show');
    
    // Rutas antiguas para compatibilidad
    Route::post('/usuarios', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->storeUsuario($request);
    })->name('usuarios.store.alternative');
    
    Route::put('/usuarios/{idUsuario}/update', function (Request $request, $idUsuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(RolesController::class)->updateUsuario($request, $idUsuario);
    })->name('usuarios.update.alternative');
});

// -------------------------------------------------------------------------
// AUTENTICACIÓN DE USUARIOS (módulo existente - SOLO ADMIN)
// -------------------------------------------------------------------------
Route::prefix('admin/usuarios-auth')->name('usuarios.auth.')->group(function () {
    Route::get('/', function (Request $request) { // CORREGIDO: Agregar Request $request
        $error = verificarAdmin();
        if ($error) return $error;
        return app(UsuariosAuthController::class)->index($request); // CORREGIDO: Pasar $request
    })->name('index');
    
    Route::post('/{idUsuario}/verificar', function (Request $request, $idUsuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(UsuariosAuthController::class)->verificarPassword($request, $idUsuario);
    })->name('verificar');
    
    Route::post('/generar-hash', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(UsuariosAuthController::class)->generarHash($request);
    })->name('generar.hash');
    
    Route::post('/verificar-global', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(UsuariosAuthController::class)->verificarGlobal($request);
    })->name('verificar.global');
});

// -------------------------------------------------------------------------
// CRUD PEDIDOS (existente - SOLO ADMIN)
// -------------------------------------------------------------------------
Route::prefix('pedidos')->name('pedidos.')->group(function () {
    Route::get('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(PedidosController::class)->index($request);
    })->name('index');
    
    Route::post('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(PedidosController::class)->store($request);
    })->name('store');
    
    Route::get('/create', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(PedidosController::class)->create($request);
    })->name('create');
    
    Route::get('/{pedido}', function (Request $request, $pedido) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(PedidosController::class)->show($request, $pedido);
    })->name('show');
    
    Route::get('/{pedido}/edit', function (Request $request, $pedido) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(PedidosController::class)->edit($request, $pedido);
    })->name('edit');
    
    Route::put('/{pedido}', function (Request $request, $pedido) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(PedidosController::class)->update($request, $pedido);
    })->name('update');
    
    Route::delete('/{pedido}', function (Request $request, $pedido) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(PedidosController::class)->destroy($request, $pedido);
    })->name('destroy');
    
    Route::prefix('{idPedido}/productos')->name('productos.')->group(function () {
        Route::get('/', function (Request $request, $idPedido) {
            $error = verificarAdmin();
            if ($error) return $error;
            return app(ProductosPedidoController::class)->index($idPedido);
        })->name('index');
        
        Route::post('/', function (Request $request, $idPedido) {
            $error = verificarAdmin();
            if ($error) return $error;
            return app(ProductosPedidoController::class)->store($request, $idPedido);
        })->name('store');
        
        Route::get('/create', function (Request $request, $idPedido) {
            $error = verificarAdmin();
            if ($error) return $error;
            return app(ProductosPedidoController::class)->create($idPedido);
        })->name('create');
        
        Route::get('/{idProducto}', function (Request $request, $idPedido, $idProducto) {
            $error = verificarAdmin();
            if ($error) return $error;
            return app(ProductosPedidoController::class)->show($idPedido, $idProducto);
        })->name('show');
        
        Route::get('/{idProducto}/edit', function (Request $request, $idPedido, $idProducto) {
            $error = verificarAdmin();
            if ($error) return $error;
            return app(ProductosPedidoController::class)->edit($idPedido, $idProducto);
        })->name('edit');
        
        Route::put('/{idProducto}', function (Request $request, $idPedido, $idProducto) {
            $error = verificarAdmin();
            if ($error) return $error;
            return app(ProductosPedidoController::class)->update($request, $idPedido, $idProducto);
        })->name('update');
        
        Route::delete('/{idProducto}', function (Request $request, $idPedido, $idProducto) {
            $error = verificarAdmin();
            if ($error) return $error;
            return app(ProductosPedidoController::class)->destroy($idPedido, $idProducto);
        })->name('destroy');
    });
});

// -------------------------------------------------------------------------
// CRUD CLIENTES (mantener para compatibilidad - SOLO ADMIN)
// -------------------------------------------------------------------------
Route::prefix('clientes')->name('clientes.')->group(function () {
    Route::get('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ClienteController::class)->index($request);
    })->name('index');
    
    Route::post('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ClienteController::class)->store($request);
    })->name('store');
    
    Route::get('/{idCliente}', function (Request $request, $idCliente) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ClienteController::class)->edit($request, $idCliente);
    })->name('edit');
    
    Route::put('/{idCliente}', function (Request $request, $idCliente) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ClienteController::class)->update($request, $idCliente);
    })->name('update');
    
    Route::delete('/{idCliente}', function (Request $request, $idCliente) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ClienteController::class)->destroy($request, $idCliente);
    })->name('destroy');
});

// -------------------------------------------------------------------------
// CRUD PRODUCTOS (existente - SOLO ADMIN)
// -------------------------------------------------------------------------
Route::prefix('productos')->name('productos.')->group(function () {
    Route::get('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProductosController::class)->index($request);
    })->name('index');
    
    Route::post('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProductosController::class)->store($request);
    })->name('store');
    
    Route::get('/{idProducto}', function (Request $request, $idProducto) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProductosController::class)->edit($request, $idProducto);
    })->name('edit');
    
    Route::put('/{idProducto}', function (Request $request, $idProducto) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProductosController::class)->update($request, $idProducto);
    })->name('update');
    
    Route::delete('/{idProducto}', function (Request $request, $idProducto) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProductosController::class)->destroy($request, $idProducto);
    })->name('destroy');
    
    Route::get('/next-id', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProductosController::class)->getNextProductId($request);
    })->name('next.id');
});

// -------------------------------------------------------------------------
// CRUD PROVEEDORES (existente - SOLO ADMIN)
// -------------------------------------------------------------------------
Route::prefix('proveedores')->name('proveedores.')->group(function () {
    Route::get('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProveedorController::class)->index($request);
    })->name('index');
    
    Route::post('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProveedorController::class)->store($request);
    })->name('store');
    
    Route::get('/create', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProveedorController::class)->create($request);
    })->name('create');
    
    Route::get('/{proveedor}', function (Request $request, $proveedor) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProveedorController::class)->show($request, $proveedor);
    })->name('show');
    
    Route::get('/{proveedor}/edit', function (Request $request, $proveedor) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProveedorController::class)->edit($request, $proveedor);
    })->name('edit');
    
    Route::put('/{proveedor}', function (Request $request, $proveedor) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProveedorController::class)->update($request, $proveedor);
    })->name('update');
    
    Route::delete('/{proveedor}', function (Request $request, $proveedor) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProveedorController::class)->destroy($request, $proveedor);
    })->name('destroy');
    
    Route::get('/siguiente-nit', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(ProveedorController::class)->getSiguienteNIT($request);
    })->name('getSiguienteNIT');
});

// -------------------------------------------------------------------------
// CRUD USUARIOS (rutas alternativas - SOLO ADMIN)
// -------------------------------------------------------------------------
Route::prefix('usuarios')->name('usuarios.')->group(function () {
    Route::get('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(UsuariosController::class)->index($request);
    })->name('index');
    
    Route::post('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(UsuariosController::class)->store($request);
    })->name('store');
    
    Route::get('/{usuario}', function (Request $request, $usuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(UsuariosController::class)->show($request, $usuario);
    })->name('show');
    
    Route::get('/{usuario}/edit', function (Request $request, $usuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(UsuariosController::class)->edit($request, $usuario);
    })->name('edit');
    
    Route::put('/{usuario}', function (Request $request, $usuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(UsuariosController::class)->update($request, $usuario);
    })->name('update');
    
    Route::delete('/{usuario}', function (Request $request, $usuario) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(UsuariosController::class)->destroy($request, $usuario);
    })->name('destroy');
});

// -------------------------------------------------------------------------
// CRUD AYUDA Y CONTACTO (existente - SOLO ADMIN)
// -------------------------------------------------------------------------
Route::prefix('ayuda-contacto')->name('AyudaContacto.')->group(function () {
    Route::get('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(AyudaContactoController::class)->index($request);
    })->name('index');
    
    Route::post('/', function (Request $request) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(AyudaContactoController::class)->store($request);
    })->name('store');
    
    Route::get('/{ayudaContacto}', function (Request $request, $ayudaContacto) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(AyudaContactoController::class)->show($request, $ayudaContacto);
    })->name('show');
    
    Route::get('/{ayudaContacto}/edit', function (Request $request, $ayudaContacto) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(AyudaContactoController::class)->edit($request, $ayudaContacto);
    })->name('edit');
    
    Route::put('/{ayudaContacto}', function (Request $request, $ayudaContacto) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(AyudaContactoController::class)->update($request, $ayudaContacto);
    })->name('update');
    
    Route::delete('/{ayudaContacto}', function (Request $request, $ayudaContacto) {
        $error = verificarAdmin();
        if ($error) return $error;
        return app(AyudaContactoController::class)->destroy($request, $ayudaContacto);
    })->name('destroy');
});

// =============================================================================
// RUTAS COMPARTIDAS
// =============================================================================

Route::get('/bienvenido', function () {
    if (!session()->has('user_id')) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión');
    }
    return view('VistasAdmin.welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    if (!session()->has('user_id')) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión');
    }
    return view('dashboard');
})->name('dashboard');

// =============================================================================
// RUTAS DE AUTENTICACIÓN
// =============================================================================

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout-get', function() {
    if (session()->has('user_id')) {
        $userName = session('user_name');
        session()->flush();
        return redirect('/')->with('success', "Sesión cerrada. ¡Adiós " . $userName . "!");
    }
    return redirect('/')->with('info', 'No había sesión activa');
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