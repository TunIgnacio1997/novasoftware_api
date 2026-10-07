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
        Schema::create('menu_e', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 30);
            $table->boolean('tabla');
            $table->integer('direccion');
            $table->integer('nivel');
            $table->integer('orden');
            $table->boolean('n');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_e');
    }
};
