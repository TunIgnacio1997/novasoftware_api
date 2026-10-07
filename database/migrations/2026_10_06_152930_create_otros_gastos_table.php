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
        Schema::create('otros_gastos', function (Blueprint $table) {
            $table->integer('folio', true);
            $table->boolean('estatus')->default(true);
            $table->date('fecha')->nullable();
            $table->string('factura_nota', 13)->default('');
            $table->boolean('nomina')->default(false);
            $table->decimal('costo', 10, 3)->default(0);
            $table->boolean('tipo_pago');
            $table->text('observaciones');
            $table->integer('id_usuario');
            $table->dateTime('id_fecha');
            $table->integer('id_sucursal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otros_gastos');
    }
};
