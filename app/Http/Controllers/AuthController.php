<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class AuthController extends Controller
{
    // Mostrar login
    public function showLogin()
    {
        return view('auth.IniciarSesion');
    }

    // Procesar login
    public function login(Request $request)
    {
        // Validar datos
        $request->validate([
            'nombreUsuario' => 'required',
            'passwordUsuario' => 'required',
        ]);

        // Buscar usuario por nombreUsuario
        $usuario = Usuario::where('nombreUsuario', $request->nombreUsuario)->first();

        // Verificar si existe y comparar hash
        if (!$usuario || !Hash::check($request->passwordUsuario, $usuario->passwordUsuario)) {
            return back()->withErrors(['error' => 'Credenciales incorrectas']);
        }

        // Iniciar sesión
        Auth::login($usuario);

        // Regenerar sesión para evitar ataques
        $request->session()->regenerate();

        // Redirigir a dashboard u otra ruta
        return redirect()->route('dashboard');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
