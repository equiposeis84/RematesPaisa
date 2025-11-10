<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

// Ruta PRINCIPAL - Esta debe existir
Route::get('/', function () {
    return view('welcome'); // o la vista que quieras usar como inicio
});

// Tus rutas de clientes
Route::resource('clientes', ClienteController::class);