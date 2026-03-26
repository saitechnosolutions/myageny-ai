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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            // Core fields
            $table->string('company_name');
            $table->string('contact_name');
            $table->date('lead_date');
            $table->string('mobile_number', 20);
            $table->string('email')->nullable();

            // Lead meta
            $table->string('lead_source');       // Reference, Ad Campaign, Direct Visit, Invitation, Cold Outreach, Social Media, Website
            $table->string('lead_status');       // New, Qualified, Proposal, Negotiation, Won, Lost
            $table->string('product_name')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high

            // Financial
            $table->decimal('deal_value', 12, 2)->nullable();

            // Notes
            $table->text('remarks')->nullable();

            // Relations
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for filter performance
            $table->index('lead_status');
            $table->index('lead_source');
            $table->index('priority');
            $table->index('lead_date');
            $table->index('branch_id');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
