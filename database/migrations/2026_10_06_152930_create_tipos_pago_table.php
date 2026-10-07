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
        Schema::create('tipos_pago', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion', 50)->nullable();
            $table->boolean('controlado');
            $table->string('descripcion2', 50)->nullable();
            $table->integer('orden');
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_pago');
    }
};
