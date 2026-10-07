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
        Schema::create('producto_relaciones', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_producto_p');
            $table->integer('id_producto_r');
            $table->timestamp('fecha')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_relaciones');
    }
};
