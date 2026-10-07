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
        Schema::create('updateimporte', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('idventadetalle');
            $table->decimal('importedetalleventa', 10, 3);
            $table->integer('idventamaster');
            $table->decimal('importeventa', 10, 3);
            $table->string('compare', 250);
            $table->integer('containCors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('updateimporte');
    }
};
