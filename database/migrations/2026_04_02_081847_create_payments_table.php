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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_product_id')
                  ->constrained('lead_products')
                  ->onDelete('cascade');

            // For quick look-up without join
            $table->foreignId('lead_id')
                  ->constrained('leads')
                  ->onDelete('cascade');

            $table->decimal('amount', 12, 2);

            $table->enum('payment_mode', [
                'cash', 'bank_transfer', 'cheque', 'upi', 'card',
            ])->default('upi');

            $table->date('payment_date');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();

            // Who recorded it
            $table->foreignId('recorded_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};