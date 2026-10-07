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
        Schema::create('inventario_ajuste', function (Blueprint $table) {
            $table->increments('id_ajuste')->unique('id_ajuste');
            $table->boolean('estatus')->unsigned()->default(false);
            $table->date('fecha');
            $table->integer('id_sucursal');
            $table->integer('id_almacen');
            $table->string('familia', 50);
            $table->string('subfamilia', 50);
            $table->boolean('solo_e')->default(false);
            $table->integer('responsable');
            $table->text('observaciones');
            $table->string('productos', 250);
            $table->unsignedInteger('id_usuario');
            $table->dateTime('id_fecha');
            $table->integer('autorizo')->default(0);
            $table->string('pdf_barcodes', 250);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_ajuste');
    }
};
