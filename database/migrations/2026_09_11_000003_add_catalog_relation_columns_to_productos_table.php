<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // `familia`, `sub_familia` and `sub_sub_familia` remain as legacy varchar display
        // columns; these new nullable FKs back the real catalog hierarchy for lookups.
        // `supplier_id` (already present) doubles as `id_proveedor`.
        Schema::table('productos', function (Blueprint $table) {
            $table->bigInteger('id_familia')->nullable()->after('sub_sub_familia');
            $table->unsignedBigInteger('id_sub_familia')->nullable()->after('id_familia');
            $table->unsignedBigInteger('id_sub_sub_familia')->nullable()->after('id_sub_familia');
            $table->integer('id_unidad_medida')->nullable()->after('id_sub_sub_familia');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['id_familia', 'id_sub_familia', 'id_sub_sub_familia', 'id_unidad_medida']);
        });
    }
};
