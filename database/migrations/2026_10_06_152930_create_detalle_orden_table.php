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
        Schema::create('detalle_orden', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_orden_compra')->index('id_orden_compra');
            $table->unsignedInteger('id_producto');
            $table->string('id_unidad_medida', 20);
            $table->decimal('pie', 10, 3)->unsigned();
            $table->decimal('cantidad', 10, 3)->unsigned();
            $table->decimal('cantidad2', 10, 3);
            $table->decimal('precio_unitario', 10, 3)->unsigned();
            $table->decimal('matanza', 10, 3);
            $table->decimal('transporte', 10, 3);
            $table->decimal('otros', 10, 3);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_orden');
    }
};
