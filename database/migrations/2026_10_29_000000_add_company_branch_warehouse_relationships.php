<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('almacenes')
            ->join('sucursales', 'sucursales.id', '=', 'almacenes.id_sucursal')
            ->whereColumn('almacenes.id_company', '<>', 'sucursales.id_company')
            ->update(['almacenes.id_company' => DB::raw('sucursales.id_company')]);

        $hasOrphanBranches = DB::table('sucursales')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('company')
                    ->whereColumn('company.id', 'sucursales.id_company');
            })
            ->exists();

        $hasOrphanWarehouses = DB::table('almacenes')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('sucursales')
                    ->whereColumn('sucursales.id', 'almacenes.id_sucursal');
            })
            ->exists();

        if ($hasOrphanBranches || $hasOrphanWarehouses) {
            throw new RuntimeException(
                'No se pueden agregar las relaciones de empresa, sucursal y almacén porque existen referencias inválidas.'
            );
        }

        Schema::table('sucursales', function (Blueprint $table) {
            $table->unique(['id', 'id_company'], 'sucursales_id_id_company_unique');
            $table->foreign('id_company', 'sucursales_id_company_foreign')
                ->references('id')
                ->on('company')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

        Schema::table('almacenes', function (Blueprint $table) {
            $table->foreign(['id_sucursal', 'id_company'], 'almacenes_sucursal_company_foreign')
                ->references(['id', 'id_company'])
                ->on('sucursales')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('almacenes', function (Blueprint $table) {
            $table->dropForeign('almacenes_sucursal_company_foreign');
        });

        Schema::table('sucursales', function (Blueprint $table) {
            $table->dropForeign('sucursales_id_company_foreign');
            $table->dropUnique('sucursales_id_id_company_unique');
        });
    }
};
