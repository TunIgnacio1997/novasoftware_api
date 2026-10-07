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
        Schema::create('ordenes_produccion', function (Blueprint $table) {
            $table->integer('id_op', true);
            $table->integer('claveop');
            $table->integer('oc')->nullable();
            $table->date('fecha');
            $table->text('notas');
            $table->integer('id_sucursal')->nullable();
            $table->integer('id_almacen');
            $table->boolean('estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_produccion');
    }
};
