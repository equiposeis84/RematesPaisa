<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Roles;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = Usuario::with('rol');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhereHas('rol', function($q) use ($search) {
                      $q->where('nombreRol', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        $datos = $query->orderBy('idUsuario', 'asc')->paginate(10);
        $roles = Roles::all();
        
        return view('usuarios')->with(compact('datos', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6',
            'idRol' => 'required|exists:roles,idRol'
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'idRol' => $request->idRol
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, $idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $idUsuario . ',idUsuario',
            'idRol' => 'required|exists:roles,idRol'
        ]);

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
            'idRol' => $request->idRol
        ];

        // Solo actualizar password si se proporciona
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy($idUsuario)
    {
        try {
            $usuario = Usuario::findOrFail($idUsuario);
            $usuario->delete();
            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}