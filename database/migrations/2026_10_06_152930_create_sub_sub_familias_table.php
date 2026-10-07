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
        Schema::create('sub_sub_familias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 50)->unique();
            $table->unsignedBigInteger('id_sub_familia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_sub_familias');
    }
};
