<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'idUsuario';
    public $incrementing = true;
    protected $keyType = 'integer';
    
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'idRol',
        'tipoDocumento',
        'documento',
        'direccion',
        'telefono',
        'activo'
    ];

    protected $hidden = [
        'password'
    ];

    public $timestamps = false;

    // Relación con rol
    public function rol()
    {
        return $this->belongsTo(Roles::class, 'idRol', 'idRol');
    }

    // Mutator para hash de password
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }
}