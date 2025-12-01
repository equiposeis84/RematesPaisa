<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Cliente::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombreCliente', 'LIKE', "%{$search}%")
<<<<<<< HEAD
=======
                    ->orWhere('idCliente', 'LIKE', "%{$search}%")
>>>>>>> 6acca2d5ca189ffb789ffba189f510767b6f6be7
                    ->orWhere('apellidoCliente', 'LIKE', "%{$search}%")
                    ->orWhere('emailCliente', 'LIKE', "%{$search}%")
<<<<<<< HEAD
=======
                    ->orWhere('NombreEmpresa', 'LIKE', "%{$search}%")
>>>>>>> 516688caa403d940564b5ec3d69001bde4adad27
                    ->orWhere('tipoDocumentoCliente', 'LIKE', "%{$search}%");
            });
        }

        $datos = $query->orderBy('idCliente', 'desc')->paginate(10);

        return view('clientes')->with('datos', $datos);
    }

    public function store(Request $request)
    {
        $request->validate([
<<<<<<< HEAD
<<<<<<< HEAD
            'idCliente'           => 'required|unique:cliente,idCliente',
            'nombreUsuario'   => 'required|string|max:40|unique:usuarios,nombreUsuario',
            'tipoDocumentoCliente'=> 'required|string|max:20',
            'nombreCliente'       => 'required|string|max:100',
            'apellidoCliente'     => 'required|string|max:100',
            'emailCliente'        => 'required|email|unique:cliente,emailCliente',
            'telefonoCliente'     => 'nullable|string|max:20',
            'direccionCliente'    => 'nullable|string|max:255',
             'passwordUsuario' => 'required|string|min:6',
        ]);

        Cliente::create([
            'idCliente'           => $request->idCliente,
            'tipoDocumentoCliente'=> $request->tipoDocumentoCliente,
            'nombreCliente'       => $request->nombreCliente,
            'apellidoCliente'     => $request->apellidoCliente,
            'emailCliente'        => $request->emailCliente,
            'telefonoCliente'     => $request->telefonoCliente,
            'direccionCliente'    => $request->direccionCliente
        ]);
        Usuario::create([
            'nombreUsuario'   => $request->emailCliente, // puedes usar email como usuario
            'passwordUsuario' => bcrypt($request->passwordUsuario),
            'idRoles'        => 1,
        ]);

                                                    //Cambiar la ruta de redirección a vista de inicio
        return redirect()->route('clientes.index') //return redirect()->route('#####') ->with('success', 'Cuenta creada correctamente. Inicia sesión.') 
            ->with('success', 'Cliente creado exitosamente');
    }

    public function edit($idCliente)
    {
        $cliente = Cliente::findOrFail($idCliente);
        return view('cliente.edit', compact('cliente'));
    }

    public function update(Request $request, $idCliente)
    {
        $cliente = Cliente::findOrFail($idCliente);

        $request->validate([
=======
            'idCliente'           => 'required|string|max:100',
            'NombreEmpresa'        => 'required|string|max:100',
            'nombreUsuario'        => 'required|string|max:40|unique:usuarios,nombreUsuario',
>>>>>>> 6acca2d5ca189ffb789ffba189f510767b6f6be7
            'tipoDocumentoCliente' => 'required|string|max:20',
            'nombreCliente'        => 'required|string|max:100',
            'apellidoCliente'      => 'required|string|max:100',
            'emailCliente'         => 'required|email|unique:cliente,emailCliente,' . $idCliente . ',idCliente',
            'telefonoCliente'      => 'nullable|string|max:20',
            'direccionCliente'     => 'nullable|string|max:255',
        ]);

<<<<<<< HEAD
        $cliente->update([
=======
        // Crear cliente
        Cliente::create([
            'idCliente'           => $request->idCliente,   // <-- LISTO
            'NombreEmpresa'        => $request->NombreEmpresa,
>>>>>>> 6acca2d5ca189ffb789ffba189f510767b6f6be7
            'tipoDocumentoCliente' => $request->tipoDocumentoCliente,
            'nombreCliente'        => $request->nombreCliente,
            'apellidoCliente'      => $request->apellidoCliente,
            'emailCliente'         => $request->emailCliente,
            'telefonoCliente'      => $request->telefonoCliente,
            'direccionCliente'     => $request->direccionCliente
        ]);

<<<<<<< HEAD
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente');
=======
        // Crear usuario asociado
        Usuario::create([
            'nombreUsuario'   => $request->emailCliente, // puedes usar email como usuario
            'passwordUsuario' => bcrypt($request->passwordUsuario),
            'idRoles'         => 1,
            'idCliente'       => $request->idCliente,
        ]);
=======
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
>>>>>>> 516688caa403d940564b5ec3d69001bde4adad27

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

<<<<<<< HEAD
        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente actualizado exitosamente');
>>>>>>> 6acca2d5ca189ffb789ffba189f510767b6f6be7
=======
            return redirect()->route('clientes.index')
                             ->with('success', 'Cliente actualizado exitosamente');

        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage())
                             ->withInput();
        }
>>>>>>> 516688caa403d940564b5ec3d69001bde4adad27
    }

    public function destroy($idCliente)
    {
        try {
            $cliente = Cliente::findOrFail($idCliente);

<<<<<<< HEAD
            $tienePedidos = \DB::table('pedidos')
                ->where('idCliente', $idCliente)
                ->exists();
=======
            // Verificar si tiene pedidos
            $tienePedidos = DB::table('pedidos')->where('idCliente', $idCliente)->exists();
>>>>>>> 516688caa403d940564b5ec3d69001bde4adad27

            if ($tienePedidos) {
                return redirect()->route('clientes.index')
                    ->with('error', 'No se puede eliminar el cliente porque tiene pedidos asociados');
            }

            // Eliminar usuario asociado primero
            DB::table('usuarios')->where('email', $cliente->emailCliente)->delete();

            // Eliminar cliente
            $cliente->delete();

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente');

        } catch (\Exception $e) {
            return redirect()->route('clientes.index')
                ->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}