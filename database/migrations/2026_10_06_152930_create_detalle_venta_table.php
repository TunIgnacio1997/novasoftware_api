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
        Schema::create('detalle_venta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_venta')->index('id_venta');
            $table->unsignedInteger('id_producto');
            $table->string('id_unidad_medida', 20);
            $table->decimal('cantidad', 10, 3)->unsigned();
            $table->decimal('cantidad2', 10, 3);
            $table->decimal('precio_unitario', 10, 3);
            $table->decimal('cantidad_surtida', 10, 3)->unsigned();
            $table->decimal('cantidad_surtida2', 10, 3);
            $table->boolean('isCore')->default(false);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_venta');
    }
};
