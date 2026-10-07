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
        Schema::create('ordenes_transferencia', function (Blueprint $table) {
            $table->integer('id_transferencia', true);
            $table->date('fecha_registro');
            $table->date('fecha_concluye');
            $table->boolean('estatus');
            $table->decimal('cantidad', 10, 3);
            $table->text('notas');
            $table->integer('empresa_p');
            $table->integer('empresa_d');
            $table->integer('sucursal_p');
            $table->integer('sucursal_d');
            $table->integer('id_tipo_pago_p');
            $table->integer('id_tipo_pago_d');
            $table->integer('id_sucursal');
            $table->integer('id_usuario');
            $table->date('id_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_transferencia');
    }
};
