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
        Schema::create('ordenes_traslado', function (Blueprint $table) {
            $table->integer('id_ot', true);
            $table->integer('claveot');
            $table->dateTime('fecha');
            $table->text('notas');
            $table->integer('id_sucursal_origen')->nullable();
            $table->integer('id_almacen_origen');
            $table->integer('id_sucursal_destino');
            $table->integer('id_almacen_destino');
            $table->boolean('estatus');
            $table->integer('id_sucursal');
            $table->integer('id_usuario');
            $table->dateTime('fecha_recibido')->nullable();
            $table->integer('id_usuario_recibio')->nullable();
            $table->dateTime('fecha_cancelado')->nullable();
            $table->integer('id_usuario_cancelo')->nullable();
            $table->text('motivo_cancelacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_traslado');
    }
};
