<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function __construct()
    {
        // Verificar que solo administradores puedan acceder
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->idRol != 1) {
                abort(403, 'Acceso no autorizado. Solo administradores.');
            }
            return $next($request);
        });
    }

    /**
     * Vista principal con pestañas (usuarios y roles)
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filter = $request->get('filter', 'all');
        $tab = $request->get('tab', 'usuarios'); // 'usuarios' o 'roles'
        
        // CONSULTA PARA USUARIOS (si la pestaña es usuarios)
        $usuariosQuery = Usuario::with('rol');
        
        if ($search && $tab == 'usuarios') {
            $usuariosQuery->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('documento', 'LIKE', "%{$search}%")
                  ->orWhere('telefono', 'LIKE', "%{$search}%")
                  ->orWhere('direccion', 'LIKE', "%{$search}%")
                  ->orWhereHas('rol', function($q) use ($search) {
                      $q->where('nombreRol', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Aplicar filtros por tipo de usuario
        if ($filter !== 'all' && $tab == 'usuarios') {
            switch($filter) {
                case 'admins':
                    $usuariosQuery->where('idRol', 1);
                    break;
                case 'clientes':
                    $usuariosQuery->where('idRol', 2);
                    break;
                case 'repartidores':
                    $usuariosQuery->where('idRol', 3);
                    break;
                case 'custom':
                    $usuariosQuery->where('idRol', '>', 3);
                    break;
            }
        }
        
        $usuarios = $usuariosQuery->orderBy('idUsuario', 'asc')->paginate(10);
        
        // CONSULTA PARA ROLES (si la pestaña es roles)
        $rolesQuery = Roles::withCount('usuarios');
        
        if ($search && $tab == 'roles') {
            $rolesQuery->where('nombreRol', 'LIKE', "%{$search}%")
                       ->orWhere('idRol', 'LIKE', "%{$search}%");
        }
        
        $roles = $rolesQuery->orderBy('idRol', 'asc')->get();
        
        // Todos los roles para dropdowns
        $todosRoles = Roles::all();
        
        // Estadísticas
        $stats = [
            'totalUsuarios' => Usuario::count(),
            'totalRoles' => Roles::count(),
            'usuariosActivos' => Usuario::where('activo', 1)->count(),
        ];
        
        return view('VistasAdmin.roles', compact(
            'usuarios', 
            'roles', 
            'todosRoles', 
            'stats',
            'tab',
            'search',
            'filter'
        ));
    }

    /**
     * =========== CRUD DE ROLES ===========
     */

    // Crear nuevo rol
    public function store(Request $request)
    {
        $request->validate([
            'idRol' => 'required|integer|unique:roles,idRol|min:4',
            'nombreRol' => 'required|string|max:45|unique:roles,nombreRol'
        ],[
            'idRol.unique' => 'El ID del rol ya existe.',
            'idRol.min' => 'Los IDs 1-3 están reservados para roles del sistema.',
            'nombreRol.unique' => 'El nombre del rol ya existe.'
        ]);

        Roles::create($request->only(['idRol', 'nombreRol']));
        
        return redirect()->route('roles.index', ['tab' => 'roles'])
            ->with('success', 'Rol creado exitosamente');
    }

    // Editar rol (formulario)
    public function edit($idRol)
    {
        $rol = Roles::findOrFail($idRol);
        return view('VistasAdmin.roles.edit', compact('rol'));
    }

    // Actualizar rol
    public function update(Request $request, $idRol)
    {
        $rol = Roles::findOrFail($idRol);
        
        $request->validate([
            'nombreRol' => 'required|string|max:45|unique:roles,nombreRol,' . $idRol . ',idRol'
        ]);
        
        $rol->update($request->only(['nombreRol']));
        
        return redirect()->route('roles.index', ['tab' => 'roles'])
            ->with('success', 'Rol actualizado exitosamente');
    }

    // Eliminar rol
    public function destroy($idRol)
    {
        try {
            $rol = Roles::findOrFail($idRol);
            
            // Verificar si hay usuarios con este rol
            if ($rol->usuarios()->count() > 0) {
                return redirect()->route('roles.index', ['tab' => 'roles'])
                    ->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
            }
            
            // No permitir eliminar roles del sistema (1, 2, 3)
            if ($idRol <= 3) {
                return redirect()->route('roles.index', ['tab' => 'roles'])
                    ->with('error', 'No se puede eliminar un rol del sistema.');
            }
            
            $rol->delete();
            
            return redirect()->route('roles.index', ['tab' => 'roles'])
                ->with('success', 'Rol eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('roles.index', ['tab' => 'roles'])
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * =========== CRUD DE USUARIOS (dentro de roles) ===========
     */

    // Crear nuevo usuario
    public function storeUsuario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6',
            'idRol' => 'required|exists:roles,idRol',
            'tipoDocumento' => 'nullable|string|max:10',
            'documento' => 'nullable|string|max:20|unique:usuarios,documento',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20'
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'idRol' => $request->idRol,
            'tipoDocumento' => $request->tipoDocumento ?? 'CC',
            'documento' => $request->documento,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'activo' => $request->has('activo') ? 1 : 0
        ]);

        return redirect()->route('roles.index', ['tab' => 'usuarios'])
            ->with('success', 'Usuario creado exitosamente');
    }

    // Actualizar usuario
    public function updateUsuario(Request $request, $idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios,email,' . $idUsuario . ',idUsuario',
            'idRol' => 'required|exists:roles,idRol',
            'tipoDocumento' => 'nullable|string|max:10',
            'documento' => 'nullable|string|max:20|unique:usuarios,documento,' . $idUsuario . ',idUsuario',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|min:6'
        ]);

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
            'idRol' => $request->idRol,
            'tipoDocumento' => $request->tipoDocumento ?? 'CC',
            'documento' => $request->documento,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'activo' => $request->has('activo') ? 1 : 0
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('roles.index', ['tab' => 'usuarios'])
            ->with('success', 'Usuario actualizado exitosamente');
    }

    // Eliminar usuario
    public function destroyUsuario($idUsuario)
    {
        try {
            // No permitir eliminar al usuario administrador principal (idUsuario = 1)
            if ($idUsuario == 1) {
                return redirect()->route('roles.index', ['tab' => 'usuarios'])
                    ->with('error', 'No se puede eliminar el usuario administrador principal.');
            }
            
            $usuario = Usuario::findOrFail($idUsuario);
            $usuario->delete();
            
            return redirect()->route('roles.index', ['tab' => 'usuarios'])
                ->with('success', 'Usuario eliminado exitosamente');
        } catch (\Exception $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('roles.index', ['tab' => 'usuarios'])
                    ->with('error', 'No se puede eliminar el usuario porque está asociado a otros registros.');
            }
            return redirect()->route('roles.index', ['tab' => 'usuarios'])
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    // Cambiar estado activo/inactivo
    public function toggleEstado($idUsuario)
    {
        $usuario = Usuario::findOrFail($idUsuario);
        $usuario->activo = $usuario->activo ? 0 : 1;
        $usuario->save();
        
        $estado = $usuario->activo ? 'activado' : 'desactivado';
        
        return redirect()->route('roles.index', ['tab' => 'usuarios'])
            ->with('success', "Usuario {$estado} exitosamente");
    }

    /**
     * =========== MÉTODOS AUXILIARES ===========
     */

    // Ver detalles de usuario
    public function verUsuario($idUsuario)
    {
        $usuario = Usuario::with(['rol'])->findOrFail($idUsuario);
        return view('VistasAdmin.usuario_detalle', compact('usuario'));
    }
}