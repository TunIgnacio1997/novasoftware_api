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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('user')->unique('users_email_unique');
                $table->string('email_verified_at', 250)->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
                $table->integer('sucursal_id')->nullable();
                $table->unsignedBigInteger('rol_id')->default(0)->index('users_rol_id_foreign');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
