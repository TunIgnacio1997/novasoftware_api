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
        Schema::create('tipos_vendedores', function (Blueprint $table) {
            $table->integer('id');
            $table->string('clave', 20);
            $table->string('nombre', 90);
            $table->dateTime('id_fecha');
            $table->integer('id_usuario');
            $table->integer('id_sucursal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_vendedores');
    }
};
