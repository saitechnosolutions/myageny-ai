<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_product_price_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_product_id')->nullable()->constrained('lead_products')->nullOnDelete();
            $table->string('deal_name');
            $table->string('product_name');
            $table->text('product_description')->nullable();
            $table->decimal('original_unit_price', 12, 2);
            $table->decimal('requested_unit_price', 12, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('rejection_reason', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_product_price_requests');
    }
};
