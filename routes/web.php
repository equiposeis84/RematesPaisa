<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductosController; 

// Ruta PRINCIPAL 
Route::get('/', function () {
    return view('welcome'); 
});


Route::resource('clientes', ClienteController::class);
Route::resource('proveedores', ProveedorController::class);
Route::resource('productos', ProductosController::class);
