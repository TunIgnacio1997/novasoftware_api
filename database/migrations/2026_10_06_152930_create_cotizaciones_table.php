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
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->increments('id_cotizacion');
            $table->string('folio_cotizacion', 20);
            $table->integer('id_cliente');
            $table->unsignedInteger('id_estatus');
            $table->string('serie', 20)->nullable();
            $table->dateTime('fecha_registro');
            $table->decimal('importe', 10, 3);
            $table->decimal('iva_aplicado', 10, 3);
            $table->integer('id_usuario');
            $table->dateTime('id_fecha');
            $table->integer('id_sucursal');
            $table->integer('id_almacen');
            $table->decimal('descuento', 10, 0)->nullable()->default(0);
            $table->integer('id_vendedor')->default(0);
            $table->string('referencia', 250)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
