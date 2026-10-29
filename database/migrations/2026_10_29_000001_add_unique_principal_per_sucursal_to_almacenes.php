<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasDuplicatePrincipals = DB::table('almacenes')
            ->select('id_sucursal')
            ->where('principal', true)
            ->groupBy('id_sucursal')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasDuplicatePrincipals) {
            throw new RuntimeException(
                'No se puede aplicar la regla de almacén principal porque hay sucursales con más de uno.'
            );
        }

        Schema::table('almacenes', function (Blueprint $table) {
            $table->integer('principal_sucursal_id')
                ->nullable()
                ->storedAs('CASE WHEN principal = 1 THEN id_sucursal ELSE NULL END');
            $table->unique('principal_sucursal_id', 'almacenes_principal_sucursal_unique');
        });
    }

    public function down(): void
    {
        Schema::table('almacenes', function (Blueprint $table) {
            $table->dropUnique('almacenes_principal_sucursal_unique');
            $table->dropColumn('principal_sucursal_id');
        });
    }
};
