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
        Schema::create('sucursales', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 100);
            $table->text('logo');
            $table->string('slogan', 100);
            $table->text('background');
            $table->string('tel', 25);
            $table->string('tel2', 25);
            $table->string('correo', 25);
            $table->string('direccion', 100);
            $table->string('colonia', 50);
            $table->string('ciudad', 50);
            $table->string('estado', 50);
            $table->smallInteger('id_company');
            $table->date('id_fecha')->nullable();
            $table->integer('id_usuario')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
