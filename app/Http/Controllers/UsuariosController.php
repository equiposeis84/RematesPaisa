<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('roles.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email', // usuarios (plural)
            'password' => 'required|min:6',
            'idRol' => 'required|exists:roles,idRol'
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'idRol' => $request->idRol
        ]);

        \Log::info('Usuario creado desde admin', [
            'id' => $usuario->idUsuario,
            'email' => $usuario->email,
            'rol' => $usuario->idRol
        ]);

        return redirect()->route('roles.index')->with('success', 'Usuario creado exitosamente');
    }

    public function update(Request $request, $idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $idUsuario . ',idUsuario', // usuarios (plural)
            'idRol' => 'required|exists:roles,idRol'
        ]);

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
            'idRol' => $request->idRol
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
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
    
    public function cambiarRol(Request $request, $idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);
        
        $request->validate([
            'idRol' => 'required|exists:roles,idRol'
        ]);

        $usuario->update([
            'idRol' => $request->idRol
        ]);

        return redirect()->route('roles.index')->with('success', 'Rol del usuario actualizado exitosamente');
    }
}