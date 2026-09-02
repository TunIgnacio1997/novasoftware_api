<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            // Relación Polimórfica (contactable_id, contactable_type)
            // Permite asociar a Client o Supplier
            $table->morphs('contactable');

            // Información Personal y Puesto
            $table->string('first_name');
            $table->string('father_last_name')->nullable();
            $table->string('mother_last_name')->nullable();
            $table->string('job_title')->nullable();

            // Canales de Comunicación
            $table->string('phone', 30)->nullable();
            $table->string('extension', 10)->nullable();
            $table->string('mobile_whatsapp', 30)->nullable();
            $table->string('email')->nullable();

            // Ubicación / Dirección
            $table->string('country', 100)->default('México');
            $table->string('state', 100)->nullable();
            $table->string('city_delegation', 100)->nullable();
            $table->string('street')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('neighborhood')->nullable();

            // Banderas / Notificaciones de Avisos (Checkboxes de la interfaz)
            $table->boolean('notify_order')->default(false);        // Aviso Pedido
            $table->boolean('notify_quote')->default(false);        // Aviso Cotización
            $table->boolean('notify_invoice')->default(false);      // Aviso Factura
            $table->boolean('notify_statement')->default(false);    // Aviso Estado Cuenta
            $table->boolean('notify_tracking')->default(false);     // Aviso Rastreo

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};