<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            // Folio único de Cotización (Ej. COT-2026-0001)
            $table->string('folio')->unique();

            $table->integer('client_id');
            $table->foreign('client_id')->references('id')->on('customers')->cascadeOnDelete();

            // Contacto específico del cliente al que se dirige la cotización (opcional)
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();

            // Vendedor / Usuario que elaboró la cotización
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Fechas
            $table->date('issued_at');
            $table->date('expires_at')->nullable();

            // Moneda
            $table->string('currency', 3)->default('MXN');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);

            // Montos acumulados
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('tax', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2)->default(0.00);

            // Estado comercial de la cotización
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'converted'])
                  ->default('draft');

            $table->text('notes')->nullable();             // Observaciones para el cliente
            $table->text('terms_conditions')->nullable(); // Condiciones comerciales / garantía

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};