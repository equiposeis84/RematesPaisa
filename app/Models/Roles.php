<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;

    protected $table = 'roles'; 
    protected $primaryKey = 'idRol';
    public $incrementing = true;
    protected $keyType = 'integer';
    
    protected $fillable = [
        'idRol',
        'nombreRol'
    ];

    public $timestamps = false;

    // Relación con usuarios
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idRol', 'idRol');
    }
}