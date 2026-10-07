<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('vendedores', 'id_users')) {
            Schema::table('vendedores', function (Blueprint $table): void {
                $table->unsignedBigInteger('id_users')->default(0)->change();
            });
        }
    }

    public function down(): void
    {
        // Keep the widened type to avoid truncating existing user IDs.
    }
};
