<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductosController; 
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ProveedorController;


// Ruta PRINCIPAL 
Route::get('/', function () {
    return view('welcome'); 
});
// rutas principales
Route::resource('clientes', ClienteController::class);
Route::resource('proveedores', ProveedorController::class);
Route::resource('productos', ProductosController::class);
Route::resource('pedidos', PedidosController::class);
// -----------------------------------------------------------------------------
Route::get('/clientes', [ClienteController::class,"index"])->name('clientes.index');
Route::post('/clientes', [ClienteController::class,"store"])->name('clientes.store');
Route::get('/clientes/{idCliente}', [ClienteController::class,"edit"])->name('clientes.edit');
Route::put('/clientes/{idCliente}', [ClienteController::class,"update"])->name('clientes.update');
Route::delete('/clientes/{idCliente}', [ClienteController::class,"destroy"])->name('clientes.destroy');
// -----------------------------------------------------------------------------
Route::get ('/productos', [ProductosController::class,"index"])->name('productos.index');
Route::post('/productos', [ProductosController::class,"store"])->name('productos.store');
Route::get('/productos/{idProducto}', [ProductosController::class,"edit"])->name('productos.edit');
Route::put('/productos/{idProducto}', [ProductosController::class,"update"])->name('productos.update');
Route::delete('/productos/{idProducto}', [ProductosController::class,"destroy"])->name('productos.destroy');
// -----------------------------------------------------------------------------
Route::get('/pedidos', [PedidosController::class,"index"])->name('pedidos.index');
Route::post('/pedidos', [PedidosController::class,"store"])->name('pedidos.store');
Route::get('/pedidos/{idPedidos}', [PedidosController::class,"edit"])->name('pedidos.edit');
Route::put('/pedidos/{idPedidos}', [PedidosController::class,"update"])->name('pedidos.update');
Route::delete('/pedidos/{idPedidos}', [PedidosController::class,"destroy"])->name('pedidos.destroy');
// -----------------------------------------------------------------------------
Route::get('/proveedores', [ProveedorController::class,"index"])->name('proveedores.index');
Route::post('/proveedores', [ProveedorController::class,"store"])->name('proveedores.store');
Route::get('/proveedores/{idProveedor}', [ProveedorController::class,"edit"])->name('proveedores.edit');
Route::put('/proveedores/{idProveedor}', [ProveedorController::class,"update"])->name('proveedores.update');
Route::delete('/proveedores/{idProveedor}', [ProveedorController::class,"destroy"])->name('proveedores.destroy');
// -----------------------------------------------------------------------------
