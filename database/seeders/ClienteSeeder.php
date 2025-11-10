<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'idCliente' => '123456789', // Número de documento
                'tipoDocumentoCliente' => 'Cédula',
                'nombreCliente' => 'Juan',
                'apellidoCliente' => 'Pérez',
                'emailCliente' => 'juan.perez@email.com',
                'telefonoCliente' => '3001234567',
                'direccionCliente' => 'Calle 123 #45-67'
            ],
            [
                'idCliente' => '987654321', // Número de documento
                'tipoDocumentoCliente' => 'Pasaporte',
                'nombreCliente' => 'María',
                'apellidoCliente' => 'González', 
                'emailCliente' => 'maria.gonzalez@email.com',
                'telefonoCliente' => '3109876543',
                'direccionCliente' => 'Avenida 456 #78-90'
            ],
            [
                'idCliente' => '456789123', // Número de documento
                'tipoDocumentoCliente' => 'Cédula',
                'nombreCliente' => 'Carlos',
                'apellidoCliente' => 'Rodríguez',
                'emailCliente' => 'carlos.rodriguez@email.com',
                'telefonoCliente' => '3205558888',
                'direccionCliente' => 'Carrera 789 #12-34'
            ]
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}