<?php
// app/Http/Middleware/AuthSession.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si hay sesión de usuario
        if (!Session::has('user_authenticated')) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a esta página');
        }
        
        // Compartir datos del usuario con todas las vistas
        if (Session::has('user_id')) {
            view()->share('current_user', [
                'id' => Session::get('user_id'),
                'name' => Session::get('user_name'),
                'email' => Session::get('user_email'),
                'role' => Session::get('user_type')
            ]);
        }
        
        return $next($request);
    }
}