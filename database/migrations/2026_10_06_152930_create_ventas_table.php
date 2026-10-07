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
        Schema::create('ventas', function (Blueprint $table) {
            $table->integer('id_venta', true);
            $table->string('folio_venta', 20);
            $table->integer('id_cliente')->index('id_cliente');
            $table->unsignedInteger('id_estatus');
            $table->string('serie', 20)->nullable();
            $table->dateTime('fecha_registro')->useCurrent();
            $table->decimal('importe', 10, 3);
            $table->decimal('iva_aplicado', 10, 3);
            $table->integer('id_usuario')->index('id_usuario');
            $table->boolean('impreso')->default(false);
            $table->string('obs', 150)->default('');
            $table->integer('id_sucursal')->index('id_sucursal');
            $table->integer('id_almacen');
            $table->integer('status_xml');
            $table->string('nombre_xml')->nullable();
            $table->decimal('descuento', 10, 0)->nullable()->default(0);
            $table->integer('id_vendedor')->default(0)->index('id_vendedor');
            $table->string('referencia', 250)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('motivo_cancelacion', 500)->nullable();

            $table->primary(['id_venta', 'fecha_registro']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
