<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Table currently has only `nombre` as its primary key and no rows; safe to restructure.
        DB::statement('ALTER TABLE `sub_sub_familias` DROP PRIMARY KEY');

        Schema::table('sub_sub_familias', function (Blueprint $table) {
            $table->bigIncrements('id')->first();
            $table->unique('nombre');
            $table->unsignedBigInteger('id_sub_familia')->nullable()->after('nombre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('sub_sub_familias', function (Blueprint $table) {
            $table->dropColumn(['id', 'id_sub_familia', 'created_at', 'updated_at']);
        });

        DB::statement('ALTER TABLE `sub_sub_familias` ADD PRIMARY KEY (`nombre`)');
    }
};
