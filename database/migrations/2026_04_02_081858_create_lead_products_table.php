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

            // Relations
            $table->foreignId('lead_id')
                  ->constrained('leads')
                  ->onDelete('cascade');

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('restrict');

            // Deal grouping — multiple products can share one deal_name
            $table->string('deal_name')->index();

            // Snapshot of product data at time of assignment
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('total_price', 12, 2)->storedAs(
                'ROUND(unit_price * quantity * (1 - discount_percent / 100), 2)'
            );

            // Per-product remarks
            $table->text('remarks')->nullable();

            // Status: new | hot | warm | cold | converted
            $table->string('product_status', 20)->default('new')->index();

            // Payment summary (denormalized for fast reads — updated by trigger/observer)
            $table->decimal('total_paid', 12, 2)->default(0);

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
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
