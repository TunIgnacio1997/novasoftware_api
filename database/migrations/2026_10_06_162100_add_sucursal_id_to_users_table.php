<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'sucursal_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('sucursal_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'sucursal_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('sucursal_id');
            });
        }
    }
};
