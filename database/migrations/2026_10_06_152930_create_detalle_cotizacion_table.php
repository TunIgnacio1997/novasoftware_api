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
        Schema::create('detalle_cotizacion', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_cotizacion');
            $table->integer('id_producto');
            $table->string('id_unidad_medida');
            $table->decimal('cantidad', 10, 0);
            $table->decimal('cantidad2', 10, 0);
            $table->decimal('precio_unitario', 10, 3);
            $table->decimal('cantidad_surtida', 10, 0);
            $table->decimal('cantidad_surtida2', 10, 0);
            $table->tinyInteger('isCore');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_cotizacion');
    }
};
