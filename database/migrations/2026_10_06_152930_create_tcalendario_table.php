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
        Schema::create('tcalendario', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fecha');
            $table->text('evento');
            $table->integer('id_usuario')->default(0)->index('id_servicio');
            $table->date('id_fecha')->nullable();
            $table->string('vistopor', 50)->default('');
            $table->tinyInteger('estado')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tcalendario');
    }
};
