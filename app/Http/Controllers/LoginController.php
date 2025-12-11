<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Session::has('user_id')) {
            $userType = Session::get('user_type');
            
            if ($userType == 1) {
                return redirect()->route('admin.inicio');
            } else {
                return redirect('/catalogo');
            }
        }
        
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        // OPCIÓN A: Usar Eloquent (Mejor)
        $user = Usuario::where('email', $request->email)->first();
        
        // OPCIÓN B: Usar DB
        // $user = DB::table('usuarios')->where('email', $request->email)->first();
        
        \Log::info('Intento de login', [
            'email' => $request->email,
            'user_found' => $user ? 'Sí' : 'No',
            'password_provided' => $request->password
        ]);
        
        if ($user) {
            \Log::info('Hash de la base de datos', ['hash' => $user->password]);
            \Log::info('Hash check result', ['check' => Hash::check($request->password, $user->password)]);
            
            if (Hash::check($request->password, $user->password)) {
                Session::put('user_id', $user->idUsuario);
                Session::put('user_name', $user->nombre);
                Session::put('user_email', $user->email);
                Session::put('user_type', $user->idRol);
                Session::put('user_authenticated', true);
                
                \Log::info('Login exitoso', [
                    'user_id' => $user->idUsuario,
                    'email' => $user->email,
                    'rol' => $user->idRol
                ]);
                
                if ($user->idRol == 1) {
                    return redirect()->route('admin.inicio')->with('success', '¡Bienvenido Administrador!');
                } else {
                    return redirect('/catalogo')->with('success', '¡Bienvenido ' . $user->nombre . '!');
                }
            }
        }
        
        \Log::warning('Login fallido', [
            'email' => $request->email,
            'existe_usuario' => $user ? 'Sí' : 'No'
        ]);
        
        return back()->withErrors([
            'email' => 'Credenciales incorrectas'
        ])->withInput($request->only('email'));
    }
    
    public function logout(Request $request)
    {
        Session::flush();
        $request->session()->regenerate();
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente');
    }
}