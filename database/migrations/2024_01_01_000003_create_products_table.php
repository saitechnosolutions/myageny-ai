<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained('product_categories')->onDelete('restrict');
            $table->string('package_name');          // Silver, Gold, Gold Plus
            $table->string('sku')->unique()->nullable();
            $table->decimal('base_price', 12, 2);
            $table->enum('tax_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('tax_value', 8, 2)->default(0);
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 8, 2)->default(0);
            $table->decimal('final_price', 12, 2);   // computed & stored
            $table->longText('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
