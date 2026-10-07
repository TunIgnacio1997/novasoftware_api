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
        Schema::create('cobratarios', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('clave', 20);
            $table->string('nombre', 90);
            $table->text('direccion');
            $table->string('telef', 25)->nullable();
            $table->string('email', 80)->nullable();
            $table->dateTime('updated_at');
            $table->integer('id_usuario');
            $table->integer('id_sucursal');
            $table->decimal('comision', 10, 0)->default(0);
            $table->tinyInteger('estatus');
            $table->string('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobratarios');
    }
};
