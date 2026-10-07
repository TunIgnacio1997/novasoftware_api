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
        Schema::create('factura_compra', function (Blueprint $table) {
            $table->integer('id_documento', true);
            $table->string('nombre');
            $table->string('ruta');
            $table->integer('id_orden');
            $table->timestamp('fecha')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_compra');
    }
};
