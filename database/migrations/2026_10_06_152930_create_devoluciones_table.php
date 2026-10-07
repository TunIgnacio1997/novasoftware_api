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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->integer('id', true);
            $table->smallInteger('id_almacen');
            $table->integer('id_usuario');
            $table->dateTime('id_fecha');
            $table->dateTime('fecha');
            $table->integer('id_orden')->index('id_orden');
            $table->integer('tipo');
            $table->boolean('estatus')->default(true);
            $table->string('motivo_cancelacion', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
