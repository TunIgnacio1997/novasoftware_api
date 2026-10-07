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
        Schema::create('quotations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('folio')->unique();
            $table->integer('client_id')->index('quotations_client_id_foreign');
            $table->unsignedBigInteger('contact_id')->nullable()->index('quotations_contact_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('quotations_user_id_foreign');
            $table->date('issued_at');
            $table->date('expires_at')->nullable();
            $table->string('currency', 3)->default('MXN');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->decimal('subtotal', 12)->default(0);
            $table->decimal('discount', 12)->default(0);
            $table->decimal('tax', 12)->default(0);
            $table->decimal('total', 12)->default(0);
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired', 'converted'])->default('draft');
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
