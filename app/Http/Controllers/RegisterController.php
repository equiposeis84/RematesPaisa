<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    public function showForm()
    {
        if (Session::has('user_id')) {
            return redirect()->route('usuario.catalogo');
        }
        
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email', // IMPORTANTE: usuarios (plural)
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'
        ], [
            'email.unique' => 'Este email ya está registrado',
            'password.confirmed' => 'Las contraseñas no coinciden'
        ]);
        
        $user = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'idRol' => 2 // Cliente por defecto
        ]);
        
        \Log::info('Usuario registrado', [
            'id' => $user->idUsuario,
            'email' => $user->email,
            'password_hash' => $user->password
        ]);
        
        if ($user) {
            Session::put('user_id', $user->idUsuario);
            Session::put('user_name', $user->nombre);
            Session::put('user_email', $user->email);
            Session::put('user_type', 2);
            Session::put('user_authenticated', true);
            
            return redirect()->route('usuario.catalogo')->with('success', '¡Registro exitoso! Bienvenido/a ' . $user->nombre);
        }
        
        return back()->with('error', 'Error al registrar usuario');
    }
}