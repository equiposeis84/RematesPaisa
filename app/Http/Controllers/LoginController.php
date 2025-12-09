<?php
// app/Http/Controllers/LoginController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB; // ← Añade esto
use App\Models\User;

class LoginController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        if (Session::has('user_id')) {
            $userType = Session::get('user_type');
            
            if ($userType == 1) {
                return redirect()->route('admin.inicio');
            } elseif ($userType == 2) {
                return redirect()->route('usuario.catalogo');
            } elseif ($userType == 3) {
                return redirect()->route('usuario.catalogo');
            }
        }
        
        return view('auth.login');
    }
    
    // Procesar login (VERSIÓN TEMPORAL - SIN MODELO)
    
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6'
    ]);
    
    $user = DB::table('usuarios')->where('email', $request->email)->first();
    
    if ($user && Hash::check($request->password, $user->password)) {
        Session::put('user_id', $user->idUsuario);
        Session::put('user_name', $user->nombre);
        Session::put('user_email', $user->email);
        Session::put('user_type', $user->idRol);
        Session::put('user_authenticated', true);
        
        // Redirigir según el rol
        if ($user->idRol == 1) { // Administrador
            return redirect()->route('admin.inicio')->with('success', '¡Bienvenido Administrador!');
        } else { // Cliente o Repartidor
            return redirect()->route('catalogo')->with('success', '¡Bienvenido!');
        }
    }
    
    return back()->withErrors([
        'email' => 'Credenciales incorrectas'
    ])->withInput($request->only('email'));
}
    
    // Cerrar sesión
    public function logout(Request $request)
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente');
    }
}