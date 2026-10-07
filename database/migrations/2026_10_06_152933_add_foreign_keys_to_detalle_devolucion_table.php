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
        Schema::table('detalle_devolucion', function (Blueprint $table) {
            $table->foreign(['id_devolucion'], 'detalle_devolucion_ibfk_1')->references(['id'])->on('devoluciones')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_devolucion', function (Blueprint $table) {
            $table->dropForeign('detalle_devolucion_ibfk_1');
        });
    }
};
