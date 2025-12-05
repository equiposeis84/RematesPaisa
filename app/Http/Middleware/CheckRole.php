<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware('role:1') or ->middleware('role:1,2')
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $idRol = null;

        if (Auth::check()) {
            $idRol = Auth::user()->idRol ?? null;
        } elseif (session()->has('idRol')) {
            $idRol = session('idRol');
        } elseif (session()->has('user')) {
            $user = session('user');
            if (is_array($user)) {
                $idRol = $user['idRol'] ?? null;
            } elseif (is_object($user)) {
                $idRol = $user->idRol ?? null;
            }
        }

        // Normalizar roles permitidos (acepta '1,2' o múltiples args)
        $allowed = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $allowed[] = $part;
                }
            }
        }

        // Si no se especificaron roles, denegar por seguridad
        if (empty($allowed)) {
            abort(403, 'No autorizado.');
        }

        // Comparar como string para cubrir int/string
        if (!in_array((string)$idRol, $allowed, true)) {
            abort(403, 'No autorizado.');
        }

        return $next($request);
    }
}
        return back()->withErrors(['error' => 'Credenciales incorrectas']);
    

    // Redirigir según rol
    private function redirectUsuario($idRol)
    {

    

    {
        switch ($idRol) {
            case 1: // Admin
                return redirect()->route('Admin.Catalogo');
            case 2: // Empleado
                return redirect()->route('Clientes.CatalogoU');

            case 3: // Proveedor
                return redirect()->route('Proveedores.CatalogoU');

            default:
                Auth::logout();
                return redirect()->route('login')->withErrors(['error' => 'Rol de usuario no reconocido']);
        }
    }
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    }