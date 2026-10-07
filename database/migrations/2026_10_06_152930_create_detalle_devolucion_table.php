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
        Schema::create('detalle_devolucion', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_devolucion')->index('id_devolucion');
            $table->dateTime('id_fecha');
            $table->integer('id_usuario');
            $table->decimal('cantidad_devuelta', 10, 0);
            $table->text('comentario');
            $table->integer('id_producto');
            $table->decimal('precio', 10)->nullable();
            $table->decimal('cantidad_original', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_devolucion');
    }
};
