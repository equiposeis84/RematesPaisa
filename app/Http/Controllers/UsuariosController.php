<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Roles;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        // Este método no debería usarse si no tenemos vista separada
        // Redirigimos a roles como fallback
        return redirect()->route('roles.index');
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

        // Redirigir de vuelta a la página de roles con mensaje de éxito
        return redirect()->route('roles.index')->with('success', 'Usuario creado exitosamente');
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

        return redirect()->route('roles.index')->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy($idUsuario)
    {
        try {
            $usuario = Usuario::findOrFail($idUsuario);
            $usuario->delete();
            return redirect()->route('roles.index')->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}