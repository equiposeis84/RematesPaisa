<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuarios;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'NombreEmpresa' => 'nullable|string|max:50',
            'nombreCliente' => 'required|string|max:50',
            'apellidoCliente' => 'required|string|max:50',
            'tipoDocumentoCliente' => 'required|string|max:20',
            'documentoCliente' => 'required|string|max:20',
            'emailCliente' => 'required|email',
            'telefonoCliente' => 'required|string|max:20',
            'direccionCliente' => 'required|string|max:150',

            // datos de usuario
            'nombreUsuario' => 'required|string|max:40|unique:usuarios,nombreUsuario',
            'passwordUsuario' => 'required|min:6|confirmed',
            'idRoles' => 'required|integer'
        ]);

        // 1. Guardar en tabla CLIENTE
        Cliente::create([
            'tipoDocumentoCliente' => $request->tipoDocumentoCliente,
            'nombreCliente' => $request->nombreCliente,
            'apellidoCliente' => $request->apellidoCliente,
            'emailCliente' => $request->emailCliente,
            'telefonoCliente' => $request->telefonoCliente,
            'direccionCliente' => $request->direccionCliente
        ]);

        // 2. Guardar en tabla USUARIOS
        Usuarios::create([
            'nombreUsuario' => $request->nombreUsuario,
            'passwordUsuario' => Hash::make($request->passwordUsuario),
            'idRoles' => $request->idRoles
        ]);

        return redirect()->route('login')->with('success', 'Registro completado.');
    }
}
