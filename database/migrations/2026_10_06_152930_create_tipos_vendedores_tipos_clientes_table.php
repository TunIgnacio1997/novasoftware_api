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
        Schema::create('tipos_vendedores_tipos_clientes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('id_vendedor', 30)->default('');
            $table->string('id_cliente', 30)->default('');
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
        Schema::dropIfExists('tipos_vendedores_tipos_clientes');
    }
};
