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
        Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('activitable_type');
            $table->unsignedBigInteger('activitable_id');
            $table->unsignedBigInteger('user_id')->nullable()->index('activities_user_id_foreign');
            $table->string('title');
            $table->enum('type', ['call', 'meeting', 'email', 'task', 'other'])->default('call');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['activitable_type', 'activitable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
