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
        Schema::create('facturacion', function (Blueprint $table) {
            $table->increments('id_factura');
            $table->unsignedInteger('id_venta');
            $table->string('serie', 1);
            $table->date('fecha_factura');
            $table->boolean('estatus_factura')->default(true);
            $table->integer('id_pago');
            $table->integer('id_otro_pago');
            $table->integer('id_usuario');
            $table->integer('tipo_usuario');
            $table->date('fecha_abono');
            $table->date('fecha_pago');
            $table->string('LugarExpedicion', 50);
            $table->string('formaDePago', 30);
            $table->string('NumCtaPago', 15);
            $table->string('Moneda', 3)->default('MXN');
            $table->decimal('subTotal', 10, 3);
            $table->decimal('total', 10, 3);
            $table->string('metodoDePago', 10);
            $table->string('tipoDeComprobante', 10)->default('ingreso');
            $table->text('conceptos');
            $table->decimal('totalImpuestosTrasladados', 10, 3);
            $table->decimal('tasa', 10, 3);
            $table->decimal('importe', 10, 3);
            $table->string('rfc', 13);
            $table->string('nombre', 30);
            $table->string('calle', 30);
            $table->string('noExterior', 10);
            $table->string('NoInterior', 10)->nullable();
            $table->string('colonia', 30);
            $table->string('municipio', 30);
            $table->string('estado', 30);
            $table->string('pais', 30);
            $table->string('codigoPostal', 5);
            $table->string('tel1', 15);
            $table->string('tel2', 15);
            $table->string('e_mail', 70);
            $table->timestamp('id_fecha')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturacion');
    }
};
