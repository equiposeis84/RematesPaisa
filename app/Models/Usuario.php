<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'nombreUsuario';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'nombreUsuario',
        'passwordUsuario',
        'idRoles'
    ];
}
