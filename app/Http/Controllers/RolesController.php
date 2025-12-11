<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filter = $request->get('filter', 'all');
        
        // Consultamos usuarios
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

        // Aplicar filtros por tipo de usuario
        if ($filter !== 'all') {
            switch($filter) {
                case 'admins':
                    $query->where('idRol', 1);
                    break;
                case 'clientes':
                    $query->where('idRol', 2);
                    break;
                case 'repartidores':
                    $query->where('idRol', 3);
                    break;
                case 'custom':
                    $query->where('idRol', '>', 3);
                    break;
            }
        }
        
        $usuarios = $query->orderBy('idUsuario', 'asc')->paginate(10);
        $roles = Roles::all(); // Para los dropdowns
        
        return view('VistasAdmin.roles')->with(compact('usuarios', 'roles'));
    }

    public function create()
    {
        return view('VistasAdmin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'idRol' => 'required|unique:roles,idRol',
            'nombreRol' => 'required|string|max:45'
        ],[
            'idRol.unique' => 'El ID del rol ya existe en la base de datos.',
        ]);

        Roles::create($request->all());
        return redirect()->route('roles.index')->with('success', 'Rol creado exitosamente');
    }

    public function edit($idRol)
    {
        $rol = Roles::findOrFail($idRol);
        return view('VistasAdmin.roles.edit', compact('rol'));
    }

    public function update(Request $request, $idRol)
    {
        $rol = Roles::findOrFail($idRol);
        
        $request->validate([
            'nombreRol' => 'required|string|max:45'
        ]);
        
        $rol->update($request->all());
        return redirect()->route('roles.index')->with('success', 'Rol actualizado exitosamente');
    }

    public function destroy($idRol)
    {
        try {
            $rol = Roles::findOrFail($idRol);
            $rol->delete();
            return redirect()->route('roles.index')->with('success', 'Rol eliminado exitosamente');
        } catch (\Exception $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('roles.index')
                    ->with('error', 'No se puede eliminar el rol porque está asociado a otros registros.');
            }
            return redirect()->route('roles.index')
                ->with('error', 'Error al eliminar el rol porque ya esta asociado: ' . $e->getMessage());
        }
    }

    // MÉTODO PARA CREAR UN NUEVO USUARIO (desde el modal de roles)
    public function storeUsuario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuario,email',
            'password' => 'required|min:6',
            'idRol' => 'required|exists:roles,idRol'
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'idRol' => $request->idRol
        ]);

        return redirect()->route('roles.index')->with('success', 'Usuario creado exitosamente');
    }

    // MÉTODO PARA EDITAR UN USUARIO
    public function updateUsuario(Request $request, $idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuario,email,' . $idUsuario . ',idUsuario',
            'idRol' => 'required|exists:roles,idRol'
        ]);

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
            'idRol' => $request->idRol
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6'
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('roles.index')->with('success', 'Usuario actualizado exitosamente');
    }

    // MÉTODO PARA ELIMINAR UN USUARIO
    public function destroyUsuario($idUsuario)
    {
        try {
            $usuario = Usuario::findOrFail($idUsuario);
            $usuario->delete();
            return redirect()->route('roles.index')->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('roles.index')
                    ->with('error', 'No se puede eliminar el usuario porque está asociado a otros registros.');
            }
            return redirect()->route('roles.index')
                ->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    // MÉTODO PARA CAMBIAR ROL DE UN USUARIO (simplificado)
    public function cambiarRol(Request $request, $idUsuario)
    {
        $request->validate([
            'idRol' => 'required|exists:roles,idRol'
        ]);

        $usuario = Usuario::findOrFail($idUsuario);
        $usuario->update(['idRol' => $request->idRol]);

        return redirect()->route('roles.index')->with('success', 'Rol cambiado exitosamente');
    }

    private function authorizeAdmin()
    {
        $idRol = null;

        if (Auth::check()) {
            $idRol = Auth::user()->idRol ?? null;
        } elseif (session()->has('idRol')) {
            $idRol = session('idRol');
        } elseif (session()->has('user')) {
            $user = session('user');
            if (is_array($user)) {
                $idRol = $user['idRol'] ?? null;
            } elseif (is_object($user)) {
                $idRol = $user->idRol ?? null;
            }
        }

        if ($idRol != 1) {
            abort(403, 'No autorizado.');
        }
    }
}