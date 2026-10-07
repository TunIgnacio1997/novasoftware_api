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
        Schema::create('detalle_inventario_ajuste', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_inventario');
            $table->integer('id_producto');
            $table->decimal('InvPC', 10, 3);
            $table->decimal('InvFisico', 10, 3);
            $table->decimal('diferencia', 10, 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_inventario_ajuste');
    }
};
