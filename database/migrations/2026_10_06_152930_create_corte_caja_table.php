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
            $table->bigIncrements('id');
            $table->date('fecha');
            $table->string('caja')->default('Caja 1');
            $table->unsignedBigInteger('id_usuario')->index('corte_caja_id_usuario_foreign');
            $table->unsignedBigInteger('id_sucursal');
            $table->decimal('efectivo_contado', 10)->default(0);
            $table->decimal('cheque_contado', 10)->default(0);
            $table->decimal('vales_contado', 10)->default(0);
            $table->decimal('tarjeta_contado', 10)->default(0);
            $table->decimal('efectivo_calculado', 10)->default(0);
            $table->decimal('cheque_calculado', 10)->default(0);
            $table->decimal('vales_calculado', 10)->default(0);
            $table->decimal('tarjeta_calculado', 10)->default(0);
            $table->decimal('efectivo_diferencia', 10)->default(0);
            $table->decimal('cheque_diferencia', 10)->default(0);
            $table->decimal('vales_diferencia', 10)->default(0);
            $table->decimal('tarjeta_diferencia', 10)->default(0);
            $table->decimal('retiro_efectivo', 10)->default(0);
            $table->decimal('retiro_cheque', 10)->default(0);
            $table->decimal('retiro_vales', 10)->default(0);
            $table->decimal('retiro_tarjeta', 10)->default(0);
            $table->decimal('total_transferencias', 10)->default(0);
            $table->decimal('total_anticipos', 10)->default(0);
            $table->decimal('total_diferencia', 10)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
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
