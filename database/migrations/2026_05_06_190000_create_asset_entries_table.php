<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_entries', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('asset_name');
            $table->string('asset_category')->nullable();
            $table->string('brand')->nullable();
            $table->string('model_name')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 12, 2)->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('warranty_expiry_date')->nullable();
            $table->enum('asset_status', ['available', 'assigned', 'in_service', 'damaged', 'retired'])->default('available');
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employee_onboardings')->nullOnDelete();
            $table->date('assigned_date')->nullable();
            $table->string('location')->nullable();
            $table->text('condition_notes')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['asset_category', 'asset_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_entries');
    }
};
