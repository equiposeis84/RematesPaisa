<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('productos_pedidos', function (Blueprint $table) {
            $table->id();
            $table->integer('idPedido');
            $table->string('idProducto', 50);
            $table->string('nombreProducto', 100);
            $table->decimal('precio', 10, 2);
            $table->integer('cantidad');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            
            $table->foreign('idPedido')->references('idPedidos')->on('pedidos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos_pedidos');
    }
};