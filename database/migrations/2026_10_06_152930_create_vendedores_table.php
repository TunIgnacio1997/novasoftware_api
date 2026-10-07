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
        Schema::create('vendedores', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('clave', 20);
            $table->string('nombre', 90);
            $table->text('direccion');
            $table->string('telef', 25)->nullable();
            $table->string('email', 80)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->integer('id_sucursal');
            $table->decimal('comision', 4, 0)->default(0);
            $table->string('tipo', 50)->default('');
            $table->unsignedBigInteger('id_users')->default(0);
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendedores');
    }
};
