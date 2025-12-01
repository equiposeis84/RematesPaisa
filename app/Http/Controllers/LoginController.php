<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login (Request $request)
    {
        $request->validate([
            'nombreUsuario' => 'required',
        'passwordUsuario' => 'required'
    ]);

        $usuario = \App\Models\Usuario::where('nombre', $request->nombreUsuario)->first();
    if (!$usuario || !Hash::check($request->passwordUsuario, $usuario->password)) {
        return back()->withErrors(['Credenciales incorrectas']);
    }

    Auth::login($usuario);
    $request->session()->regenerate();

    return redirect()->intended('/');
}
}
