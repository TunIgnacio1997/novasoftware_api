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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->increments('id_movimiento');
            $table->unsignedInteger('id_producto');
            $table->string('id_unidad_medida', 20);
            $table->dateTime('fecha_movimiento');
            $table->decimal('cantidad', 10, 3);
            $table->string('tipo', 50);
            $table->integer('transaccion');
            $table->decimal('existencia_anterior', 10, 3);
            $table->decimal('existencia_posterior', 10, 3);
            $table->dateTime('id_fecha');
            $table->unsignedInteger('id_usuario');
            $table->integer('id_almacen')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
