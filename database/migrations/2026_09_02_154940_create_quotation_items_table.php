<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();

            $table->integer('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('productos')->nullOnDelete();

            $table->text('description');
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('tax_percentage', 5, 2)->default(16.00); // IVA base (16%)

            // Totales por línea
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
