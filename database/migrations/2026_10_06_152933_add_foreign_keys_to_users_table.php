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
        Schema::table('users', function (Blueprint $table) {
            // 1. Crear la columna rol_id si no existe
            if (!Schema::hasColumn('users', 'rol_id')) {
                $table->unsignedBigInteger('rol_id')->default(0)->after('id');
            }

            // 2. Agregar la clave foránea
            $table->foreign('rol_id')
                  ->references('id')
                  ->on('roles')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Eliminar la clave foránea protegiendo con try-catch por si no existe en la BD
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['rol_id']);
            });
        } catch (\Throwable $e) {
            // Si la clave foránea no existía en MySQL, se ignora el error para continuar
        }

        // 2. Eliminar la columna solo si existe en la tabla
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'rol_id')) {
                $table->dropColumn('rol_id');
            }
        });
    }
};
