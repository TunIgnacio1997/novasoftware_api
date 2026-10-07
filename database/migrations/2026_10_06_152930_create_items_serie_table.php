<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items_serie', function (Blueprint $table) {
            $table->unsignedInteger('id_producto');
            $table->string('serie', 50);
            $table->boolean('estatus')->default(true);
            $table->integer('clave_almacen');
            $table->smallInteger('id_company');
            $table->integer('id_sucursal');
            $table->unsignedInteger('id_orden_compra');
            $table->unsignedInteger('id_venta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_serie');
    }
};
