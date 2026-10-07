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
        Schema::create('movimientos_cuentas_clientes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('estatus', 10)->default('P');
            $table->integer('id_cliente');
            $table->dateTime('fecha')->nullable()->useCurrent();
            $table->decimal('cargo', 10, 3)->default(0);
            $table->decimal('abono', 10, 3)->default(0);
            $table->decimal('saldo', 10, 4)->default(0);
            $table->decimal('restante', 10, 3)->default(0);
            $table->text('notas');
            $table->text('referencia');
            $table->integer('id_venta')->default(0);
            $table->tinyInteger('tipo_pago')->default(0);
            $table->tinyInteger('movimiento')->default(0);
            $table->dateTime('vence')->nullable();
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
            $table->integer('id_cobratario')->default(0);
            $table->dateTime('updated_at')->useCurrent();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->integer('id_usuario_cancelacion')->nullable();
            $table->tinyInteger('is_cargo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_cuentas_clientes');
    }
};
