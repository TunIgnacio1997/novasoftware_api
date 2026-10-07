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
        Schema::create('productos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('item_name', 100);
            $table->enum('estatus', ['P', 'X', 'C'])->nullable()->default('P');
            $table->string('size', 30)->default('1');
            $table->string('item_number', 20)->nullable();
            $table->tinyText('description')->nullable();
            $table->integer('brand_id')->default(0);
            $table->smallInteger('category_id')->default(0);
            $table->integer('supplier_id')->default(0);
            $table->enum('type_id', ['C', 'S', 'K', 'T'])->default('C')->comment('Control,Servicio,Kit,Token');
            $table->string('unit_m', 50)->default('');
            $table->decimal('buy_price', 7)->default(0);
            $table->decimal('unit_price', 7)->default(0);
            $table->string('supplier_item_number', 20)->nullable();
            $table->decimal('tax_percent', 5)->default(0);
            $table->decimal('total_cost', 7)->default(0);
            $table->decimal('quantity', 7)->nullable()->default(0);
            $table->decimal('reorder_level', 7)->nullable()->default(0);
            $table->decimal('max_level', 7)->default(0);
            $table->string('image', 80)->nullable()->default('images/items/item.gif');
            $table->string('tipo', 50)->default('DISTRIBUIDORA');
            $table->string('familia', 50)->default('');
            $table->string('sub_familia', 50)->default('');
            $table->string('sub_sub_familia', 50)->default('');
            $table->bigInteger('id_familia')->nullable();
            $table->unsignedBigInteger('id_sub_familia')->nullable();
            $table->unsignedBigInteger('id_sub_sub_familia')->nullable();
            $table->integer('id_unidad_medida')->nullable();
            $table->decimal('maximo', 10, 3)->default(0);
            $table->decimal('minimo', 10, 3)->default(0);
            $table->boolean('inventario')->default(false);
            $table->boolean('congelado')->default(false);
            $table->boolean('subproducto')->default(false);
            $table->integer('pertenece')->nullable();
            $table->boolean('maneja_series')->default(false);
            $table->string('location', 20)->nullable()->default('');
            $table->boolean('allow_core');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
