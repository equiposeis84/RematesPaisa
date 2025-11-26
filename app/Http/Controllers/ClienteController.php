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
            $query->where(function($q) use ($search) {
                $q->where('nombreCliente', 'LIKE', "%{$search}%")
                  ->orWhere('apellidoCliente', 'LIKE', "%{$search}%")
                  ->orWhere('emailCliente', 'LIKE', "%{$search}%")
                  ->orWhere('idCliente', 'LIKE', "%{$search}%")
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
    'NombreEmpresa'        => 'required|string|max:100',
    'idCliente'            => 'required|unique:cliente,idCliente',
    'nombreUsuario'        => 'required|string|max:40|unique:usuarios,nombreUsuario',
    'tipoDocumentoCliente' => 'required|string|max:20',
    'nombreCliente'        => 'required|string|max:100',
    'apellidoCliente'      => 'required|string|max:100',
    'emailCliente'         => 'required|email|unique:cliente,emailCliente',
    'telefonoCliente'      => 'nullable|string|max:20',
    'direccionCliente'     => 'nullable|string|max:255',
    'passwordUsuario'      => 'required|string|min:6',
], [
    'NombreEmpresa.required'        => 'El nombre de la empresa es obligatorio.',
    'idCliente.required'            => 'El ID del cliente es obligatorio.',
    'idCliente.unique'              => 'El ID del cliente ya está registrado.',
    'nombreUsuario.required'        => 'El nombre de usuario es obligatorio.',
    'nombreUsuario.unique'          => 'El nombre de usuario ya está registrado.',
    'tipoDocumentoCliente.required' => 'El tipo de documento es obligatorio.',
    'nombreCliente.required'        => 'El nombre del cliente es obligatorio.',
    'apellidoCliente.required'      => 'El apellido del cliente es obligatorio.',
    'emailCliente.required'         => 'El correo electrónico es obligatorio.',
    'emailCliente.email'            => 'El formato del correo no es válido.',
    'emailCliente.unique'           => 'El correo ya está registrado.',
    'passwordUsuario.required'      => 'La contraseña es obligatoria.',
    'passwordUsuario.min'           => 'La contraseña debe tener mínimo 6 caracteres.'
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
        'direccionCliente'     => 'required|string|max:45',
        'telefonoCliente'      => 'required|string|max:45',
        'emailCliente'         => 'required|email|unique:cliente,emailCliente,' . $idCliente . ',idCliente',
    ], [
        'emailCliente.unique'  => 'El email ya está en uso por otro cliente.',
    ]);

    $cliente->update([
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
}


   
    public function destroy($idCliente){
        try{
            $cliente = Cliente::findOrFail($idCliente);
            
            // Verificar si el cliente tiene pedidos asociados
            $tienePedidos = \DB::table('pedidos')->where('idCliente', $idCliente)->exists();
            
            if ($tienePedidos) {
                return redirect()->route('clientes.index')->with('error', 'No se puede eliminar el cliente porque tiene pedidos asociados');
            }
            
            $cliente->delete();
            return redirect()->route('clientes.index')->with('success', 'Cliente eliminado exitosamente');
            
        }catch(\Exception $e){
            return redirect()->route('clientes.index')->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}
