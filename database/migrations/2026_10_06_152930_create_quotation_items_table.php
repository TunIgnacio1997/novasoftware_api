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
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('quotation_id')->index('quotation_items_quotation_id_foreign');
            $table->integer('product_id')->nullable()->index('quotation_items_product_id_foreign');
            $table->text('description');
            $table->decimal('quantity', 10)->default(1);
            $table->decimal('unit_price', 12)->default(0);
            $table->decimal('discount_percentage', 5)->default(0);
            $table->decimal('tax_percentage', 5)->default(16);
            $table->decimal('subtotal', 12);
            $table->decimal('total', 12);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
