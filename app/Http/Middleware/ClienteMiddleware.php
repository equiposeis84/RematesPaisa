<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si está autenticado como cliente (sesión personalizada)
        if (!session('cliente_id')) {
            return redirect()->route('login')->with('error', 'Por favor inicia sesión como cliente.');
        }

        return $next($request);
    }
}