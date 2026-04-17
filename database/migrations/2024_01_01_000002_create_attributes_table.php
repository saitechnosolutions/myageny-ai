<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->constrained('product_categories')->onDelete('cascade');
            $table->string('name');           // e.g. "Leads Per Month"
            $table->string('key');            // e.g. "leads_per_month"
            $table->enum('field_type', ['text', 'number', 'select', 'textarea', 'checkbox'])->default('text');
            $table->text('options')->nullable(); // JSON array for select options
            $table->string('unit')->nullable();  // e.g. "Rs", "%", "/month"
            $table->string('placeholder')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_category_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
