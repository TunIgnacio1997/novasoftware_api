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
        Schema::create('detalle_fondo_fijo', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_fondo_fijo');
            $table->boolean('tipo_fondo_fijo');
            $table->date('fecha');
            $table->integer('folio');
            $table->decimal('cantidad', 10, 3);
            $table->boolean('tabla');
            $table->integer('registro');
            $table->string('estatus', 5);
            $table->boolean('id_estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_fondo_fijo');
    }
};
