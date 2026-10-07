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
        Schema::table('corte_caja', function (Blueprint $table) {
            $table->foreign(['id_usuario'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corte_caja', function (Blueprint $table) {
            $table->dropForeign('corte_caja_id_usuario_foreign');
        });
    }
};
