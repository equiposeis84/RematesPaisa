<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios'; // nombre exacto

      protected $fillable = [
        'nombreUsuario',
        'passwordUsuario',
        'idRoles'
    ];

    public $timestamps = false;

    // Importante: evitar hashing doble
    protected $hidden = ['contrasena'];
}
