<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Verificar si usuario está autenticado
    public function checkAuth()
    {
        return response()->json([
            'authenticated' => Session::has('user_id'),
            'user' => Session::get('user_name'),
            'role' => Session::get('user_type')
        ]);
    }
    
    // Obtener información del usuario actual
    public function getUserInfo()
    {
        if (Session::has('user_id')) {
            return response()->json([
                'id' => Session::get('user_id'),
                'name' => Session::get('user_name'),
                'email' => Session::get('user_email'),
                'role' => Session::get('user_type')
            ]);
        }
        
        return response()->json(['error' => 'No autenticado'], 401);
    }
}