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
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('contactable_type');
            $table->unsignedBigInteger('contactable_id');
            $table->string('first_name');
            $table->string('father_last_name')->nullable();
            $table->string('mother_last_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('extension', 10)->nullable();
            $table->string('mobile_whatsapp', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('country', 100)->default('México');
            $table->string('state', 100)->nullable();
            $table->string('city_delegation', 100)->nullable();
            $table->string('street')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('neighborhood')->nullable();
            $table->boolean('notify_order')->default(false);
            $table->boolean('notify_quote')->default(false);
            $table->boolean('notify_invoice')->default(false);
            $table->boolean('notify_statement')->default(false);
            $table->boolean('notify_tracking')->default(false);
            $table->timestamps();

            $table->index(['contactable_type', 'contactable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
