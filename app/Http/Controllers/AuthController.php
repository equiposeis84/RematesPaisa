<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Mostrar formulario (ya tienes la vista)
    public function showLogin()
    {
        return view('auth.login');
    }

    // Procesar login
    public function login(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'passwordUsuario' => 'required|string'
        ]);

        $usuario = Usuario::where('nombreUsuario', $request->nombreUsuario)->first();

        if (!$usuario) {
            return back()->withErrors(['nombreUsuario' => 'Usuario no encontrado'])->withInput();
        }

        $stored = $usuario->passwordUsuario;

        // Soporte para contraseñas hasheadas o en texto plano (transición)
        $valid = false;
        if (Hash::check($request->passwordUsuario, $stored)) {
            $valid = true;
        } elseif ($request->passwordUsuario === $stored) {
            $valid = true;
        }

        if (!$valid) {
            return back()->withErrors(['passwordUsuario' => 'Contraseña incorrecta'])->withInput();
        }

        // Guardar usuario en session (sencillo)
        Session::put('usuario', [
            'nombreUsuario' => $usuario->nombreUsuario,
            'idRoles' => $usuario->idRoles
        ]);

        // Redirigir según rol (ajusta los nombres de rol si tu DB usa otros valores)
        if (strtolower($usuario->idRoles) === 'admin' || $usuario->idRoles == 1) {
            return redirect()->route('admin.inicio');
        }

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Session::forget('usuario');
        return redirect()->route('login');
    }
}
