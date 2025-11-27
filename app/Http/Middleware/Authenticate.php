<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si no hay usuario en sesión, redirige al login
        if (!auth()->check()) {
            return redirect('/login');
        }

        return $next($request);
    }
}
