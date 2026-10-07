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
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->foreign(['product_id'])->references(['id'])->on('productos')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['quotation_id'])->references(['id'])->on('quotations')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropForeign('quotation_items_product_id_foreign');
            $table->dropForeign('quotation_items_quotation_id_foreign');
        });
    }
};
