<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Los roles permitidos (ej: "1" o "2,3")
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Verificar si el usuario está autenticado
        if (!Session::has('user_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión primero');
        }
        
        // 2. Obtener el rol del usuario actual
        $userRole = Session::get('user_type');
        
        // 3. Si solo viene un parámetro con comas (ej: "2,3"), convertirlo a array
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = explode(',', $roles[0]);
        }
        
        // 4. Verificar si el usuario tiene alguno de los roles permitidos
        if (!in_array($userRole, $roles)) {
            // Acceso denegado - Redirigir según el rol actual
            if ($userRole == 1) {
                return redirect()->route('admin.inicio')
                    ->with('error', 'No tienes acceso a esta sección');
            } else {
                return redirect()->route('catalogo')
                    ->with('error', 'Acceso restringido. Solo administradores.');
            }
        }
        
        // 5. Si todo está bien, continuar
        return $next($request);
    }
}