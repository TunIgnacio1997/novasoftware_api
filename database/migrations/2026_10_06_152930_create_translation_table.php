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
        Schema::create('translation', function (Blueprint $table) {
            $table->integer('id_translation');
            $table->char('id_language', 2)->comment('es, en, ..');
            $table->text('text');

            $table->primary(['id_translation', 'id_language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation');
    }
};
