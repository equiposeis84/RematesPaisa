<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuariosAuthController extends Controller
{
    /**
     * Mostrar la vista de autenticación de usuarios
     */
    public function index(Request $request = null) // Hacer $request opcional
    {
        // Verificar que el usuario sea admin (idRol == 1)
        if (!session()->has('user_id') || session('user_type') != 1) {
            return redirect()->route('login')->with('error', 'Acceso restringido a administradores');
        }
        
        // Manejar el caso cuando $request es null
        $search = $request ? $request->get('search') : '';
        
        $query = Usuario::with('rol')->orderBy('idUsuario', 'asc');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        $usuarios = $query->paginate(10);
        
        return view('VistasAdmin.usuarios-auth', compact('usuarios', 'search'));
    }
    
    /**
     * Verificar una contraseña específica
     */
    public function verificarPassword(Request $request, $idUsuario)
    {
        if (!session()->has('user_id') || session('user_type') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        $request->validate([
            'password_intento' => 'required|string|min:1'
        ]);
        
        $usuario = Usuario::findOrFail($idUsuario);
        
        // Intentar verificar la contraseña
        $esCorrecta = Hash::check($request->password_intento, $usuario->password);
        
        return response()->json([
            'success' => true,
            'es_correcta' => $esCorrecta,
            'mensaje' => $esCorrecta ? '✓ Contraseña correcta' : '✗ Contraseña incorrecta'
        ]);
    }
    
    /**
     * Generar hash de una contraseña (para demostración)
     */
    public function generarHash(Request $request)
    {
        if (!session()->has('user_id') || session('user_type') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        $request->validate([
            'password' => 'required|string|min:1'
        ]);
        
        $hash = Hash::make($request->password);
        
        return response()->json([
            'success' => true,
            'hash' => $hash,
            'password_original' => $request->password
        ]);
    }
    
    /**
     * Verificar todos los usuarios con una contraseña común
     */
    public function verificarGlobal(Request $request)
    {
        if (!session()->has('user_id') || session('user_type') != 1) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        $request->validate([
            'password_comun' => 'required|string|min:1'
        ]);
        
        $usuarios = Usuario::all();
        $resultados = [];
        
        foreach ($usuarios as $usuario) {
            $esCorrecta = Hash::check($request->password_comun, $usuario->password);
            $resultados[] = [
                'id' => $usuario->idUsuario,
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'es_correcta' => $esCorrecta,
                'hash' => $usuario->password
            ];
        }
        
        return response()->json([
            'success' => true,
            'password_probada' => $request->password_comun,
            'resultados' => $resultados,
            'total' => count($usuarios),
            'correctos' => collect($resultados)->where('es_correcta', true)->count()
        ]);
    }
}