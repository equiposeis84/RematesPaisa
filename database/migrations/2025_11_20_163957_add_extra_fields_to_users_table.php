<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {

        $table->string('empresa')->nullable();
        $table->string('nitEmpresa')->nullable();
        $table->string('nombre');
        $table->string('apellido');
        $table->string('tipoDocumento');
        $table->string('documento')->unique();
        $table->string('telefono');
        $table->string('direccion');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'empresa',
            'nitEmpresa',
            'nombre',
            'apellido',
            'tipoDocumento',
            'documento',
            'telefono',
            'direccion'
        ]);
    });
}

};
