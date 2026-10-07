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
        Schema::create('company', function (Blueprint $table) {
            $table->smallInteger('id', true);
            $table->string('conceptnamecompany', 100);
            $table->string('mail', 100);
            $table->string('url')->nullable();
            $table->longText('logo');
            $table->string('access_key', 21)->nullable()->unique('access_key');
            $table->text('direccion');
            $table->decimal('iva', 5)->default(0);
            $table->string('rfc', 15);
            $table->string('regimen_fiscal')->default('Persona física con actividad empresarial y profesional');
            $table->boolean('onboarding_completed')->default(false);
            $table->enum('catalog_import_mode', ['file', 'skipped'])->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company');
    }
};
