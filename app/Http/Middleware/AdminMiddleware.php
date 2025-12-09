<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Verificar si el usuario autenticado es admin (rol 1)
        if (Auth::user()->idRol != 1) {
            return redirect()->route('home')->with('error', 'Acceso no autorizado.');
        }

        return $next($request);
    }
}