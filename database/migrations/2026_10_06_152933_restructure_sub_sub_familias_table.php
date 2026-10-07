<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Quitar la clave primaria antigua (nombre) si existe
        try {
            DB::statement('ALTER TABLE `sub_sub_familias` DROP PRIMARY KEY');
        } catch (\Throwable $e) {
            // Ignorar si la clave primaria ya no estaba asignada a 'nombre'
        }

        // 2. Modificar la estructura de la tabla
        Schema::table('sub_sub_familias', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_sub_familias', 'id')) {
                $table->bigIncrements('id')->first();
            }

            if (!Schema::hasColumn('sub_sub_familias', 'id_sub_familia')) {
                $table->unsignedBigInteger('id_sub_familia')->nullable()->after('nombre');
            }

            if (!Schema::hasColumn('sub_sub_familias', 'created_at')) {
                $table->timestamps();
            }
        });

        // 3. Asignar el índice único a 'nombre' protegiendo contra duplicados
        try {
            Schema::table('sub_sub_familias', function (Blueprint $table) {$table->unique('nombre');
            });
        } catch (\Throwable $e) {
            // Ignorar si el índice único ya existía en MySQL
        }
    }

    public function down(): void
    {
        Schema::table('sub_sub_familias', function (Blueprint $table) {
            // 1. Quitar el índice único de 'nombre'
            try {
                $table->dropUnique(['nombre']);
            } catch (\Throwable $e) {}

            // 2. Quitar columnas secundarias
            $columnsToDrop = [];
            foreach (['id_sub_familia', 'created_at', 'updated_at'] as $column) {
                if (Schema::hasColumn('sub_sub_familias', $column)) {
                    $columnsToDrop[] =$column;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        if (Schema::hasColumn('sub_sub_familias', 'id')) {
            // 3. Quitar AUTO_INCREMENT de 'id' antes de drop
            DB::statement('ALTER TABLE `sub_sub_familias` MODIFY `id` BIGINT UNSIGNED NOT NULL');

            try {
                DB::statement('ALTER TABLE `sub_sub_familias` DROP PRIMARY KEY');
            } catch (\Throwable $e) {}

            Schema::table('sub_sub_familias', function (Blueprint $table) {$table->dropColumn('id');
            });
        }

        // 4. Restablecer 'nombre' como PRIMARY KEY original
        try {
            DB::statement('ALTER TABLE `sub_sub_familias` ADD PRIMARY KEY (`nombre`)');
        } catch (\Throwable $e) {}
    }
};
