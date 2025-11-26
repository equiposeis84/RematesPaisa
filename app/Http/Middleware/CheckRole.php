<?php



namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
     public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check() || auth()->user()->idRoles != $role) {
            return redirect('/'); // o a donde desees
        }

        return $next($request);
    }
}
