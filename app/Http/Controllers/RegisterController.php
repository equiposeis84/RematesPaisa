<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('VistasCliente.registro');
    }

    public function register(Request $request)
    {
        $request->validate([
            'NombreEmpresa'        => 'nullable|string|max:100',
            'idCliente'            => 'required|string|max:40|unique:cliente,idCliente',
            'tipoDocumentoCliente' => 'required|string|max:45',
            'nombreCliente'        => 'required|string|max:45',
            'apellidoCliente'      => 'required|string|max:45',
            'direccionCliente'     => 'required|string|max:45',
            'telefonoCliente'      => 'required|string|max:45',
            'emailCliente'         => 'required|email|max:45|unique:cliente,emailCliente',

            // Para tabla usuarios
            'nombre'   => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:usuarios,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // 1. Guardar CLIENTE (datos personales)
        Cliente::create([
            'NombreEmpresa'        => $request->NombreEmpresa,
            'idCliente'            => $request->idCliente,
            'tipoDocumentoCliente' => $request->tipoDocumentoCliente,
            'nombreCliente'        => $request->nombreCliente,
            'apellidoCliente'      => $request->apellidoCliente,
            'direccionCliente'     => $request->direccionCliente,
            'telefonoCliente'      => $request->telefonoCliente,
            'emailCliente'         => $request->emailCliente,
            'idRol'                => 2 // Cliente
        ]);

        // 2. Guardar USUARIO (datos de login)
        Usuario::create([
            'nombre'   => $request->nombre,
            'email'    => $request->email,
            'password' => Hash::make($request->password),  // Hash de contraseña
            'idRol'    => 2
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Registro completado correctamente.');
    }
}
