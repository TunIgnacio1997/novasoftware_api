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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('num_proveedor', 20)->default('');
            $table->string('razon_social', 90);
            $table->string('nombre_comercial', 90);
            $table->enum('clasif', ['A', 'B', 'U', 'S'])->nullable();
            $table->string('calle', 80)->nullable();
            $table->string('num_ext', 20)->nullable();
            $table->char('num_int', 10)->nullable()->default('');
            $table->char('cruzamiento', 30)->nullable()->default('');
            $table->char('colonia', 50)->nullable()->default('');
            $table->string('cod_post', 30)->nullable();
            $table->char('ciudad', 50)->nullable();
            $table->string('municipio', 80)->nullable()->default('');
            $table->string('estado')->nullable();
            $table->string('telef1', 25)->nullable();
            $table->string('telef2', 25)->nullable()->default('');
            $table->string('email', 80)->nullable();
            $table->char('contacto', 40)->nullable()->default('');
            $table->char('asesor', 40)->nullable()->default('');
            $table->text('comments')->nullable();
            $table->string('rfc', 15);
            $table->string('curp', 20);
            $table->enum('excl_dual', ['E', 'D'])->default('E');
            $table->decimal('credito', 9, 0);
            $table->integer('dias');
            $table->integer('tiempo_entrega');
            $table->boolean('bloqueo');
            $table->decimal('saldo', 10, 3)->default(0);
            $table->integer('id_company');
            $table->decimal('tax', 7)->nullable()->default(0);
            $table->tinyInteger('estatus');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
