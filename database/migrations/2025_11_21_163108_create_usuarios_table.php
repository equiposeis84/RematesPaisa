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
    Schema::create('usuarios', function (Blueprint $table) {
        $table->string('nombreUsuario', 40)->primary();
        $table->string('passwordUsuario', 255);
        $table->integer('idRoles');
        $table->unsignedBigInteger('idCliente'); // enlace con cliente

        $table->foreign('idCliente')->references('idCliente')->on('cliente')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('usuarios');
}

};
