<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            ['idRol' => 1, 'nombreRol' => 'Admin'],
            ['idRol' => 2, 'nombreRol' => 'Cliente'],
            ['idRol' => 3, 'nombreRol' => 'Repartidor']
        ]);
    }
}