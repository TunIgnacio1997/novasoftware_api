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
        Schema::create('customers', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('num_cliente', 11)->index('num_cliente');
            $table->string('razon_social', 90);
            $table->string('nombre_comercial', 90);
            $table->enum('clasif', ['A', 'B', 'U', 'S'])->nullable();
            $table->string('calle', 80)->nullable()->default('');
            $table->string('num_ext', 20)->nullable()->default('');
            $table->char('num_int', 10)->nullable()->default('');
            $table->char('cruzamiento', 30)->nullable()->default('');
            $table->char('colonia', 50)->nullable()->default('');
            $table->string('cod_post', 30)->nullable()->default('');
            $table->char('ciudad', 50)->nullable()->default('');
            $table->string('municipio', 80)->nullable()->default('');
            $table->char('estado', 10)->nullable()->default('');
            $table->string('telef1', 25)->nullable()->default('');
            $table->string('telef2', 25);
            $table->string('email', 80)->nullable()->default('');
            $table->char('contacto', 40);
            $table->char('asesor', 40);
            $table->text('comments')->nullable();
            $table->string('rfc', 15);
            $table->string('curp', 20);
            $table->enum('excl_dual', ['E', 'D'])->default('E');
            $table->decimal('credito', 10, 3)->default(0);
            $table->integer('plazo')->default(0);
            $table->boolean('pagos')->default(false);
            $table->string('tipo', 50)->default('');
            $table->boolean('bloqueo')->default(false);
            $table->decimal('saldo', 10, 3)->default(0);
            $table->integer('id_reparticion');
            $table->integer('id_cobratario');
            $table->integer('id_company');
            $table->string('domicilio_residencia', 80)->default('');
            $table->decimal('tax', 7)->nullable()->default(0);
            $table->tinyInteger('estatus');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->string('created_at', 250);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
