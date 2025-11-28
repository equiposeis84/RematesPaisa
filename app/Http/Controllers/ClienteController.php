<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

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

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente creado exitosamente');
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
            'idCliente'           => 'required|string|max:100',
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

        $cliente->update([
            'idCliente'           => $request->idCliente,
            'NombreEmpresa'        => $request->NombreEmpresa,
            'tipoDocumentoCliente' => $request->tipoDocumentoCliente,
            'nombreCliente'        => $request->nombreCliente,
            'apellidoCliente'      => $request->apellidoCliente,
            'direccionCliente'     => $request->direccionCliente,
            'telefonoCliente'      => $request->telefonoCliente,
            'emailCliente'         => $request->emailCliente,

        ]);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente actualizado exitosamente');
>>>>>>> 6acca2d5ca189ffb789ffba189f510767b6f6be7
    }

    public function destroy($idCliente)
    {
        try {
            $cliente = Cliente::findOrFail($idCliente);

            $tienePedidos = \DB::table('pedidos')
                ->where('idCliente', $idCliente)
                ->exists();

            if ($tienePedidos) {
                return redirect()->route('clientes.index')
                    ->with('error', 'No se puede eliminar el cliente porque tiene pedidos asociados');
            }

            $cliente->delete();

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente');

        } catch (\Exception $e) {
            return redirect()->route('clientes.index')
                ->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}
