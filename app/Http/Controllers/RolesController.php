<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;
use App\Models\Usuario;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filter = $request->get('filter', 'all'); // Nuevo parámetro de filtro
        
        $query = Roles::withCount('usuarios');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('idRol', 'LIKE', "%{$search}%")
                  ->orWhere('nombreRol', 'LIKE', "%{$search}%");
            });
        }

        // Aplicar filtros por tipo de rol
        if ($filter !== 'all') {
            switch($filter) {
                case 'admins':
                    $query->where('idRol', 1); // Administradores
                    break;
                case 'clientes':
                    $query->where('idRol', 2); // Clientes
                    break;
                case 'repartidores':
                    $query->where('idRol', 3); // Repartidores
                    break;
                case 'custom':
                    $query->where('idRol', '>', 3); // Roles personalizados
                    break;
            }
        }
        
        $datos = $query->orderBy('idRol', 'asc')->paginate(10);
        
        return view('roles')->with('datos', $datos);
    }

    // ... el resto de tus métodos se mantienen igual ...
    public function create()
    {
        return view('roles.create');
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
        return view('roles.edit', compact('rol'));
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

    // MÉTODO CORREGIDO: Obtener usuarios para asignar a rol
    public function getUsuariosParaRol($idRol)
    {
        \Log::info("Solicitando usuarios para rol: " . $idRol);
        
        try {
            $usuarios = Usuario::with('rol')->get();
            
            \Log::info("Usuarios encontrados: " . $usuarios->count());
            
            $usuariosMapeados = $usuarios->map(function($usuario) use ($idRol) {
                return [
                    'idUsuario' => $usuario->idUsuario,
                    'nombre' => $usuario->nombre,
                    'email' => $usuario->email,
                    'idRol' => $usuario->idRol,
                    'rol_actual' => $usuario->rol ? $usuario->rol->nombreRol : 'Sin rol',
                    'seleccionado' => $usuario->idRol == $idRol
                ];
            });
            
            \Log::info("Usuarios mapeados: " . json_encode($usuariosMapeados));
            
            return response()->json($usuariosMapeados);
            
        } catch (\Exception $e) {
            \Log::error("Error al obtener usuarios: " . $e->getMessage());
            return response()->json([], 500);
        }
    }

    // MÉTODO: Asignar usuarios al rol
    public function asignarUsuarios(Request $request, $idRol)
    {
        $usuariosSeleccionados = $request->input('usuarios', []);
        
        // Actualizar los usuarios seleccionados
        Usuario::whereIn('idUsuario', $usuariosSeleccionados)
               ->update(['idRol' => $idRol]);
        
        return redirect()->route('roles.index')
               ->with('success', 'Usuarios asignados al rol exitosamente');
    }
}