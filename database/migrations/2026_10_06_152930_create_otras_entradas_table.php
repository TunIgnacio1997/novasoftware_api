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
        Schema::create('otras_entradas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->boolean('estatus');
            $table->date('fecha');
            $table->decimal('cantidad', 10, 0);
            $table->boolean('tipo_pago');
            $table->string('acreedor', 90);
            $table->text('nota');
            $table->date('vencimiento');
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
        Schema::dropIfExists('otras_entradas');
    }
};
