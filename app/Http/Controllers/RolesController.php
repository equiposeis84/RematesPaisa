<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = Roles::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('idRol', 'LIKE', "%{$search}%")
                  ->orWhere('nombreRol', 'LIKE', "%{$search}%")
                  ->orWhere('descripcionRol', 'LIKE', "%{$search}%");
            });
        }
        
        $datos = $query->orderBy('idRol', 'asc')->paginate(10);
        
        return view('roles')->with('datos', $datos);
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'idRol' => 'required|unique:roles,idRol',
            'nombreRol' => 'required|string|max:45',
            'descripcionRol' => 'nullable|string|max:255'
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
            'nombreRol' => 'required|string|max:45',
            'descripcionRol' => 'nullable|string|max:255'
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
}   