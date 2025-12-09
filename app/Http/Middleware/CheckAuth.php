<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors([
                'error' => 'Debes iniciar sesión para acceder a esta página.'
            ]);
        }

        // Si se especificaron roles, verificar que el usuario tenga alguno
        if (!empty($roles)) {
            $userRol = Auth::user()->idRol;
            
            if (!in_array($userRol, $roles)) {
                abort(403, 'No tienes permisos para acceder a esta página.');
            }
        }

        return $next($request);
    }
}