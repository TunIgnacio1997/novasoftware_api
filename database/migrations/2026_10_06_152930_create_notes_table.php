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
        Schema::create('notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('notable_type');
            $table->unsignedBigInteger('notable_id');
            $table->unsignedBigInteger('user_id')->nullable()->index('notes_user_id_foreign');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();

            $table->index(['notable_type', 'notable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
