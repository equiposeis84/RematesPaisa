<?php
// app/Http/Middleware/RedirectIfAuthenticated.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RedirectIfAuthenticated
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
        // Si ya está autenticado, redirigir según su rol
        if (Session::has('user_id')) {
            $userRole = Session::get('user_type');
            
            if ($userRole == 1) {
                return redirect()->route('admin.inicio');
            } else {
                return redirect()->route('usuario.catalogo');
            }
        }
        
        return $next($request);
    }
}