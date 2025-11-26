<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    public function handle($request, Closure $next, ...$rolesPermitidos)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return redirect('/auth/login');
        }

        if (!in_array($usuario->idRoles, $rolesPermitidos)) {
            return redirect('/')->with('error', 'No tienes permisos');
        }

        return $next($request);
    }
}
