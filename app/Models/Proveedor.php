<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores'; 
    
    protected $primaryKey = 'NITProveedores';
    
    public $incrementing = false;
    
    protected $keyType = 'int';
    
    protected $fillable = [
        'NITProveedores',
        'nombreProveedor',
        'telefonoProveedor',
        'correoProveedor'
    ];

    public $timestamps = false;

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Productos::class, 'NITProveedores', 'NITProveedores');
    }
}