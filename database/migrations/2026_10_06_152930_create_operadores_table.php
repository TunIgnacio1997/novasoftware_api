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
        Schema::create('operadores', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 90)->default('');
            $table->string('calle', 80)->default('');
            $table->char('colonia', 50)->default('');
            $table->string('licencia', 20)->default('');
            $table->string('numero', 25)->default('');
            $table->string('telefono', 25)->default('');
            $table->string('email', 80)->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operadores');
    }
};
