<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendedores', function (Blueprint $table): void {
            $table->unsignedBigInteger('id_usuario')->nullable()->change();
        });
    }

    public function down(): void
    {
        // The base vendedores schema also allows a null id_usuario.
    }
};
