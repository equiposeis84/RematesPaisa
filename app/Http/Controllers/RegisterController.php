<?php
// app/Http/Controllers/RegisterController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    // Mostrar formulario de registro
    public function showForm()
    {
        // Si ya está autenticado, redirigir
        if (Session::has('user_id')) {
            return redirect()->route('usuario.catalogo');
        }
        
        return view('auth.register');
    }
    
    // Procesar registro
    public function register(Request $request)
    {
        // 1. Validar datos
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6|confirmed', // confirmed valida password_confirmation
            'password_confirmation' => 'required|min:6'
        ], [
            'email.unique' => 'Este email ya está registrado',
            'password.confirmed' => 'Las contraseñas no coinciden'
        ]);
        
        // 2. Crear nuevo usuario
        $user = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Encriptar contraseña
            'idRol' => 2 // TODOS los nuevos usuarios son CLIENTES (rol 2)
        ]);
        
        // 3. Iniciar sesión automáticamente
        if ($user) {
            Session::put('user_id', $user->idUsuario);
            Session::put('user_name', $user->nombre);
            Session::put('user_email', $user->email);
            Session::put('user_type', 2); // Cliente
            Session::put('user_authenticated', true);
            
            return redirect()->route('usuario.catalogo')->with('success', '¡Registro exitoso! Bienvenido/a ' . $user->nombre);
        }
        
        // 4. Si hay error, regresar
        return back()->with('error', 'Error al registrar usuario');
    }
}