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
        Schema::create('detalle_ot', function (Blueprint $table) {
            $table->integer('id_detalle', true);
            $table->integer('id_ot');
            $table->integer('id_producto');
            $table->string('id_unidad_medida', 6);
            $table->decimal('cantidad', 10, 3);
            $table->decimal('cantidad2', 10, 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ot');
    }
};
