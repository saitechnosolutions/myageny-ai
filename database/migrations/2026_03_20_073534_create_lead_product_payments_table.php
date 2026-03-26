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
        Schema::create('lead_product_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_product_id')->constrained('lead_products')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode');             // cash, bank_transfer, cheque, upi, card
            $table->date('payment_date');
            $table->string('reference_number')->nullable(); // cheque no / UTR / transaction ID
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('lead_product_id');
            $table->index('lead_id');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_product_payments');
    }
};