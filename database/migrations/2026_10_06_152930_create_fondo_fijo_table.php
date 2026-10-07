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
        Schema::create('fondo_fijo', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fecha')->useCurrent();
            $table->decimal('ventas_efectivo', 10, 3)->nullable()->default(0);
            $table->decimal('ventas_banco', 10, 3)->nullable()->default(0);
            $table->decimal('abonos_clie_efectivo', 10, 3)->nullable()->default(0);
            $table->decimal('abonos_clie_banco', 10, 3)->nullable()->default(0);
            $table->decimal('compras_efectivo', 10, 3)->nullable()->default(0);
            $table->decimal('compras_banco', 10, 3)->nullable()->default(0);
            $table->decimal('abonos_prov_efectivo', 10, 3)->nullable()->default(0);
            $table->decimal('abonos_prov_banco', 10, 3)->nullable()->default(0);
            $table->decimal('otros_gastos_efectivo', 10, 3)->nullable()->default(0);
            $table->decimal('otros_gastos_banco', 10, 3)->nullable()->default(0);
            $table->decimal('otras_entradas_efectivo', 10, 3)->nullable();
            $table->decimal('otras_entradas_banco', 10, 3)->nullable();
            $table->decimal('ingresos_efectivo', 10, 3)->nullable()->default(0);
            $table->decimal('ingresos_banco', 10, 3)->nullable()->default(0);
            $table->decimal('egresos_efectivo', 10, 3)->nullable()->default(0);
            $table->decimal('egresos_banco', 10, 3)->nullable()->default(0);
            $table->decimal('efectivo_inicial', 10, 3)->nullable()->default(0);
            $table->decimal('efectivo_final', 10, 3)->nullable()->default(0);
            $table->decimal('banco_inicial', 10, 3)->nullable()->default(0);
            $table->decimal('banco_final', 10, 3)->nullable()->default(0);
            $table->string('notas', 50)->nullable();
            $table->integer('id_sucursal');
            $table->boolean('estatus')->default(false);
            $table->integer('id_usuario');
            $table->dateTime('fecha_apertura')->nullable();
            $table->dateTime('fecha_cierre')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->decimal('efectivo_real', 10, 4)->nullable();
            $table->decimal('diferencia_efectivo', 10, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fondo_fijo');
    }
};
