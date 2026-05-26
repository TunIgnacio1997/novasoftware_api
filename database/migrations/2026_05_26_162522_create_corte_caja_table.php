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
    Schema::create('corte_caja', function (Blueprint $table) {
        $table->id();
        $table->date('fecha');
        $table->string('caja')->default('Caja 1');
        $table->unsignedBigInteger('id_usuario');
        $table->unsignedBigInteger('id_sucursal');

        // Contado (lo que el cajero cuenta físicamente)
        $table->decimal('efectivo_contado', 10, 2)->default(0);
        $table->decimal('cheque_contado',   10, 2)->default(0);
        $table->decimal('vales_contado',    10, 2)->default(0);
        $table->decimal('tarjeta_contado',  10, 2)->default(0);

        // Calculado (lo que el sistema calcula de las ventas)
        $table->decimal('efectivo_calculado', 10, 2)->default(0);
        $table->decimal('cheque_calculado',   10, 2)->default(0);
        $table->decimal('vales_calculado',    10, 2)->default(0);
        $table->decimal('tarjeta_calculado',  10, 2)->default(0);

        // Diferencias
        $table->decimal('efectivo_diferencia', 10, 2)->default(0);
        $table->decimal('cheque_diferencia',   10, 2)->default(0);
        $table->decimal('vales_diferencia',    10, 2)->default(0);
        $table->decimal('tarjeta_diferencia',  10, 2)->default(0);

        // Retiros por corte
        $table->decimal('retiro_efectivo', 10, 2)->default(0);
        $table->decimal('retiro_cheque',   10, 2)->default(0);
        $table->decimal('retiro_vales',    10, 2)->default(0);
        $table->decimal('retiro_tarjeta',  10, 2)->default(0);

        // Totales extra
        $table->decimal('total_transferencias', 10, 2)->default(0);
        $table->decimal('total_anticipos',      10, 2)->default(0);
        $table->decimal('total_diferencia',     10, 2)->default(0);

        $table->text('observaciones')->nullable();
        $table->timestamps();

        $table->foreign('id_usuario')->references('id')->on('users');
        $table->foreign('id_sucursal')->references('id')->on('sucursales');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corte_caja');
    }
};
