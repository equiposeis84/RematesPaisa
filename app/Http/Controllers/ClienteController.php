<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//Cliente ahora es usuario

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Cliente::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombreCliente', 'LIKE', "%{$search}%")
                    ->orWhere('idCliente', 'LIKE', "%{$search}%")
                    ->orWhere('apellidoCliente', 'LIKE', "%{$search}%")
                    ->orWhere('emailCliente', 'LIKE', "%{$search}%")
                    ->orWhere('NombreEmpresa', 'LIKE', "%{$search}%")
                    ->orWhere('tipoDocumentoCliente', 'LIKE', "%{$search}%");
            });
        }

        $datos = $query->orderBy('idCliente', 'desc')->paginate(10);

        return view('clientes')->with('datos', $datos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'idCliente'           => 'required|string|max:100|unique:cliente,idCliente',
            'NombreEmpresa'        => 'required|string|max:100',
            'tipoDocumentoCliente' => 'required|string|max:20',
            'nombreCliente'        => 'required|string|max:100',
            'apellidoCliente'      => 'required|string|max:100',
            'emailCliente'         => 'required|email|unique:cliente,emailCliente',
            'telefonoCliente'      => 'required|string|max:20',
            'direccionCliente'     => 'required|string|max:255',
            'passwordUsuario'      => 'required|string|min:6',
        ], [
            'NombreEmpresa.required'        => 'El nombre de la empresa es obligatorio.',
            'idCliente.required'            => 'El ID del cliente es obligatorio.',
            'idCliente.unique'              => 'El ID del cliente ya está registrado.',
            'tipoDocumentoCliente.required' => 'El tipo de documento es obligatorio.',
            'nombreCliente.required'        => 'El nombre del cliente es obligatorio.',
            'apellidoCliente.required'      => 'El apellido del cliente es obligatorio.',
            'emailCliente.required'         => 'El correo electrónico es obligatorio.',
            'emailCliente.email'            => 'El formato del correo no es válido.',
            'emailCliente.unique'           => 'El correo ya está registrado.',
            'telefonoCliente.required'      => 'El teléfono es obligatorio.',
            'direccionCliente.required'     => 'La dirección es obligatoria.',
            'passwordUsuario.required'      => 'La contraseña es obligatoria.',
            'passwordUsuario.min'           => 'La contraseña debe tener mínimo 6 caracteres.'
        ]);

        try {
            DB::beginTransaction();

            // Crear cliente
            Cliente::create([
                'idCliente'           => $request->idCliente,
                'NombreEmpresa'        => $request->NombreEmpresa,
                'tipoDocumentoCliente' => $request->tipoDocumentoCliente,
                'nombreCliente'        => $request->nombreCliente,
                'apellidoCliente'      => $request->apellidoCliente,
                'emailCliente'         => $request->emailCliente,
                'telefonoCliente'      => $request->telefonoCliente,
                'direccionCliente'     => $request->direccionCliente,
                'idRol'                => 2 // Valor por defecto para clientes
            ]);

            // Crear usuario asociado - CORREGIDO con la estructura correcta de la tabla usuarios
            Usuario::create([
                'nombre'   => $request->nombreCliente . ' ' . $request->apellidoCliente,
                'email'    => $request->emailCliente,
                'password' => bcrypt($request->passwordUsuario),
                'idRol'    => 2, // Rol Cliente
            ]);

            DB::commit();

            return redirect()->route('clientes.index')
                             ->with('success', 'Cliente creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'Error al crear el cliente: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function edit($idCliente)
    {
        $cliente = Cliente::findOrFail($idCliente);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $idCliente)
    {
        $cliente = Cliente::findOrFail($idCliente);

        $request->validate([
            'NombreEmpresa'        => 'required|string|max:100',
            'tipoDocumentoCliente' => 'required|string|max:20',
            'nombreCliente'        => 'required|string|max:100',
            'apellidoCliente'      => 'required|string|max:100',
            'direccionCliente'     => 'required|string|max:255',
            'telefonoCliente'      => 'required|string|max:45',
            'emailCliente'         => 'required|email|unique:cliente,emailCliente,' . $idCliente . ',idCliente'
        ], [
            'emailCliente.unique'  => 'El email ya está en uso por otro cliente.',
        ]);

        try {
            $cliente->update([
                'NombreEmpresa'        => $request->NombreEmpresa,
                'tipoDocumentoCliente' => $request->tipoDocumentoCliente,
                'nombreCliente'        => $request->nombreCliente,
                'apellidoCliente'      => $request->apellidoCliente,
                'direccionCliente'     => $request->direccionCliente,
                'telefonoCliente'      => $request->telefonoCliente,
                'emailCliente'         => $request->emailCliente,
            ]);

            // Actualizar también el usuario asociado
            $usuario = Usuario::where('email', $cliente->emailCliente)->first();
            if ($usuario) {
                $usuario->update([
                    'nombre' => $request->nombreCliente . ' ' . $request->apellidoCliente,
                    'email'  => $request->emailCliente,
                ]);
            }

            return redirect()->route('clientes.index')
                             ->with('success', 'Cliente actualizado exitosamente');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function destroy($idCliente)
    {
        try {
            $cliente = Cliente::findOrFail($idCliente);

            // Verificar si tiene pedidos
            $tienePedidos = DB::table('pedidos')->where('idCliente', $idCliente)->exists();

            if ($tienePedidos) {
                return redirect()->route('clientes.index')->with('error', 'No se puede eliminar el cliente porque tiene pedidos asociados');
            }

            // Eliminar usuario asociado primero
            DB::table('usuarios')->where('email', $cliente->emailCliente)->delete();

            // Eliminar cliente
            $cliente->delete();

            return redirect()->route('clientes.index')->with('success', 'Cliente eliminado exitosamente');

        } catch (\Exception $e) {
            return redirect()->route('clientes.index')->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}