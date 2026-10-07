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
        Schema::create('detalle_mermas', function (Blueprint $table) {
            $table->integer('id_merma');
            $table->decimal('cantidad', 10, 3);
            $table->text('comentario');
            $table->dateTime('id_fecha');
            $table->integer('id_producto')->nullable();
            $table->integer('id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_mermas');
    }
};
