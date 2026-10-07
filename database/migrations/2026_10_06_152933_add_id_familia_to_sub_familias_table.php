<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullable, no DB-level FK constraint: legacy `familias.id` is a signed bigint,
        // integrity for this relation is enforced in the import service layer instead.
        Schema::table('sub_familias', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_familias', 'id_familia')) {
                $table->bigInteger('id_familia')->nullable()->after('nombre');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sub_familias', function (Blueprint $table) {
            if (Schema::hasColumn('sub_familias', 'id_familia')) {
                $table->dropColumn('id_familia');
            }
        });
    }
};
