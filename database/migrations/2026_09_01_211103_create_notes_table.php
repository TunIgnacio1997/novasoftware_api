<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            // Relación Polimórfica (notable_id, notable_type)
            $table->morphs('notable');

            // Opcional: Usuario autor de la nota
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->text('content');
            $table->boolean('is_pinned')->default(false); // Para fijar notas importantes arriba

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};