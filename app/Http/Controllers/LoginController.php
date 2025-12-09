<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        // Si ya tiene sesión, redirigir según rol
        if (Session::has('user_id')) {
            $userType = Session::get('user_type');
            
            if ($userType == 1) {
                return redirect()->route('admin.inicio');
            } else { // Rol 2 o 3
                return redirect('/catalogo'); // Redirige a la ruta /catalogo
            }
        }
        
        // Si no tiene sesión, mostrar login
        return view('auth.login');
    }
    
    // Procesar login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        $user = DB::table('usuarios')->where('email', $request->email)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            // Crear sesión
            Session::put('user_id', $user->idUsuario);
            Session::put('user_name', $user->nombre);
            Session::put('user_email', $user->email);
            Session::put('user_type', $user->idRol);
            Session::put('user_authenticated', true);
            
            // DEBUG: Mostrar en consola
            \Log::info('Login exitoso', [
                'user_id' => $user->idUsuario,
                'user_name' => $user->nombre,
                'user_type' => $user->idRol
            ]);
            
            // Redirigir según rol
            if ($user->idRol == 1) { // Administrador
                return redirect()->route('admin.inicio')->with('success', '¡Bienvenido Administrador!');
            } else { // Cliente o Repartidor (2 o 3)
                // Redirige a la ruta CATÁLOGO
                return redirect('/catalogo')->with('success', '¡Bienvenido ' . $user->nombre . '!');
            }
        }
        
        \Log::warning('Login fallido', ['email' => $request->email]);
        
        return back()->withErrors([
            'email' => 'Credenciales incorrectas'
        ])->withInput($request->only('email'));
    }
    
    // Cerrar sesión
    public function logout(Request $request)
    {
        Session::flush();
        $request->session()->regenerate();
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente');
    }
}