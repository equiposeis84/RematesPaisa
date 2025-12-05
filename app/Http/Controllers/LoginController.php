<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use App\Models\Cliente;

class LoginController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('VistasCliente.IniciarSesion');
    }

    // Procesar login
    public function login(Request $request)
    {
        // Validar que vengan email y password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;

        // 1. Buscar en la tabla usuarios (empleados/admin)
        $usuario = Usuario::where('email', $email)->first();

        if ($usuario && Hash::check($password, $usuario->password)) {
            // Iniciar sesión como Usuario
            Auth::login($usuario);
            // Colocar aquí: $request->session()->regenerate();
            // (Regeneración de sesión documentada; activar según flujo de sesión del proyecto)

            // Redirigir según el rol del usuario
            return $this->redirectUsuario($usuario->idRol);
        }

        // 2. Buscar en la tabla clientes
        $cliente = Cliente::where('emailCliente', $email)->first();

        if ($cliente && isset($cliente->password) && Hash::check($password, $cliente->password)) {
            // Iniciar sesión como Cliente (usando guard 'cliente' si está configurado, o sesión personalizada)
            // Por ahora, usaremos sesión personalizada para el cliente
            Auth::logout(); // Cerrar cualquier sesión de usuario
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Guardar datos del cliente en sesión
            session(['cliente_id' => $cliente->idCliente]);
            session(['cliente_nombre' => $cliente->nombreCliente]);
            session(['cliente_email' => $cliente->emailCliente]);
            session(['user_type' => 'cliente']);

            return redirect()->route('cliente.catalogo');
        }

        // Si no se encontró en ninguna tabla
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    private function redirectUsuario($roleId)
    {
        switch ($roleId) {
            case 1: // Admin
                return redirect()->route('admin.inicio');
            case 2: // Cliente (pero es un usuario con rol 2, que podría tener cliente asociado)
                // Si tiene cliente asociado, redirigir al catálogo de cliente
                $usuario = Auth::user();
                if ($usuario->cliente) {
                    // También podríamos guardar en sesión que es cliente a través de usuario
                    session(['cliente_id' => $usuario->cliente->idCliente]);
                    return redirect()->route('cliente.catalogo');
                }
                // Si no, redirigir a dashboard o donde corresponda
                return redirect()->route('dashboard');
            case 3: // Proveedor (si existe)
                return redirect()->route('proveedor.dashboard');
            case 4: // Empleado
                return redirect()->route('empleado.dashboard');
            default:
                return redirect('/');
        }
    }

    public function logout(Request $request)
    {
        // Si hay sesión de cliente, limpiarla
        if (session('user_type') == 'cliente') {
            session()->forget(['cliente_id', 'cliente_nombre', 'cliente_email', 'user_type']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}