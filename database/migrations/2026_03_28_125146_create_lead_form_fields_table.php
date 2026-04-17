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
         Schema::create('lead_form_fields', function (Blueprint $table) {
            $table->id();
            $table->string('label');                          // Display label
            $table->string('field_name')->unique();           // snake_case key used in API / storage
            $table->enum('field_type', [
                'text', 'number', 'select', 'radio', 'textarea', 'date', 'email', 'phone'
            ])->default('text');
            $table->string('placeholder')->nullable();
            $table->text('default_value')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
 
            // ---- Number / Calculation fields ----
            $table->boolean('is_calculation')->default(false);   // Does this field take part in a formula?
            $table->text('calculation_formula')->nullable();      // e.g. "field_a * field_b"  (stored as JSON or expression)
            $table->string('calculation_label')->nullable();      // Human-readable formula description
 
            // ---- Select / Radio options ----
            // Stored as JSON array: [{"label":"Option A","value":"option_a"}, ...]
            $table->json('options')->nullable();
 
            // Validation rules stored as JSON  e.g. {"min":0,"max":9999999}
            $table->json('validation_rules')->nullable();
 
            $table->unsignedBigInteger('branch_id')->nullable(); // NULL = global; set = branch-specific
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_form_fields');
    }
};