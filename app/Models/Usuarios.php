<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios'; // nombre exacto

    protected $fillable = [
        'nombre',
        'correo',
        'contrasena',
        'rol'
    ];

    public $timestamps = false;

    // Importante: evitar hashing doble
    protected $hidden = ['contrasena'];
}
