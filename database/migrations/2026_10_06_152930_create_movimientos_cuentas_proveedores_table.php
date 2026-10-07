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
        Schema::create('movimientos_cuentas_proveedores', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('estatus', 10)->default('P');
            $table->integer('id_proveedor');
            $table->date('fecha')->nullable();
            $table->decimal('cargo', 10, 3)->default(0);
            $table->decimal('abono', 10, 3)->default(0);
            $table->decimal('restante', 10, 3)->default(0);
            $table->text('notas');
            $table->text('referencia');
            $table->integer('id_compra')->default(0);
            $table->tinyInteger('tipo_pago')->default(0);
            $table->tinyInteger('movimiento')->default(0);
            $table->date('vence')->nullable();
            $table->integer('devolucion')->default(0);
            $table->integer('recibo')->default(0);
            $table->decimal('tipo_cambio', 5, 3)->default(0);
            $table->decimal('cargousd', 10, 3)->default(0);
            $table->decimal('abonousd', 10, 3)->default(0);
            $table->decimal('restanteusd', 10, 3)->default(0);
            $table->integer('origen')->default(0);
            $table->boolean('nota_credito')->default(false);
            $table->integer('id_usuario');
            $table->dateTime('id_fecha');
            $table->integer('id_sucursal');
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->integer('id_usuario_cancelacion')->nullable();
            $table->boolean('is_cargo')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_cuentas_proveedores');
    }
};
