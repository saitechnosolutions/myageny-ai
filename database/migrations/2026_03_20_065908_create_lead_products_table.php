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
         Schema::create('lead_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);   // computed: (unit_price * qty) - discount
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->string('payment_status')->default('pending'); // pending, partial, paid
            $table->date('payment_date')->nullable();
            $table->string('payment_mode')->nullable();           // cash, bank_transfer, cheque, upi, card
            $table->text('payment_notes')->nullable();
            $table->timestamps();

            $table->index('lead_id');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_products');
    }
};
