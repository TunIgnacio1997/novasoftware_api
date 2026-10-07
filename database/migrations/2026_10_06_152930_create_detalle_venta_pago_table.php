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
        Schema::create('detalle_venta_pago', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_venta');
            $table->integer('id_metodo');
            $table->string('saldo', 250);
            $table->string('importe_recibido', 250);
            $table->decimal('cambio', 10, 4);
            $table->decimal('monto_aplicado', 10, 4);
            $table->timestamp('fecha')->useCurrentOnUpdate()->useCurrent();
            $table->string('notes', 250);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_venta_pago');
    }
};
