<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nombre de la tabla
    protected $table = 'usuarios';
    
    // Clave primaria
    protected $primaryKey = 'idUsuario';
    
    // Timestamps automáticos
    public $timestamps = true;
    
    // Campos que se pueden llenar
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'idRol'
    ];
    
    // Campos ocultos
    protected $hidden = [
        'password'
    ];
    
    // Método para encriptar contraseña automáticamente
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}