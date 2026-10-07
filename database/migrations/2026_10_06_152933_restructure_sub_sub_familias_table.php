<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tabla = 'sub_sub_familias';

    public function up(): void
    {
        $tabla = $this->tabla;

        // 1. Quitar la PK actual solo si está sobre 'nombre'
        $pk = $this->primaryIndex();
        if ($pk && $pk['columns'] === ['nombre']) {
            Schema::table($tabla, function (Blueprint $table) use ($pk) {
                $table->dropPrimary($pk['name']);
            });
        }

        // 2. Agregar id como nueva PK (llamada separada: el orden importa)
        if (!Schema::hasColumn($tabla, 'id')) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->id()->first();
            });
        }

        // 3. Columnas nuevas
        if (!Schema::hasColumn($tabla, 'id_sub_familia')) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->unsignedBigInteger('id_sub_familia')->nullable();
            });
        }

        if (!Schema::hasColumn($tabla, 'created_at')) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasColumn($tabla, 'updated_at')) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }

        // 4. Índice único en 'nombre' si no existe
        if (!$this->hasUniqueOn('nombre')) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->unique('nombre');
            });
        }
    }

    public function down(): void
    {
        $tabla = $this->tabla;

        // 1. Quitar único de 'nombre'
        foreach (Schema::getIndexes($tabla) as $index) {
            if ($index['unique'] && !$index['primary'] && $index['columns'] === ['nombre']) {
                Schema::table($tabla, function (Blueprint $table) use ($index) {
                    $table->dropUnique($index['name']);
                });
            }
        }

        // 2. Quitar columnas (al borrar 'id' se elimina también su PK)
        $drop = array_values(array_filter(
            ['id_sub_familia', 'created_at', 'updated_at', 'id'],
            fn ($c) => Schema::hasColumn($tabla, $c)
        ));
        if ($drop) {
            Schema::table($tabla, function (Blueprint $table) use ($drop) {
                $table->dropColumn($drop);
            });
        }

        // 3. Restablecer PK en 'nombre'
        if (!$this->primaryIndex() && Schema::hasColumn($tabla, 'nombre')) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->primary('nombre');
            });
        }
    }

    private function primaryIndex(): ?array
    {
        foreach (Schema::getIndexes($this->tabla) as $index) {
            if ($index['primary']) {
                return $index;
            }
        }
        return null;
    }

    private function hasUniqueOn(string $column): bool
    {
        foreach (Schema::getIndexes($this->tabla) as $index) {
            if ($index['unique'] && !$index['primary'] && $index['columns'] === [$column]) {
                return true;
            }
        }
        return false;
    }
};
