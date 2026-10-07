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
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_proveedor');
            $table->unsignedInteger('id_estatus');
            $table->boolean('id_tipo_pago');
            $table->string('referencia', 100)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('fecha_recepcion')->useCurrent();
            $table->decimal('iva_aplicado', 10, 3)->default(0);
            $table->decimal('importe', 10, 3)->unsigned();
            $table->unsignedInteger('id_usuario');
            $table->dateTime('updated_at')->useCurrent();
            $table->decimal('descuento', 10)->default(0);
            $table->integer('id_sucursal');
            $table->integer('id_almacen');
            $table->boolean('mp')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_compra');
    }
};
