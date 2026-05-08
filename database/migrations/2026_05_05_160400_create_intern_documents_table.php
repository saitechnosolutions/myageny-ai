<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intern_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_joining_form_id')->constrained('intern_joining_forms')->cascadeOnDelete();
            $table->string('document_10th_marksheet')->nullable();
            $table->string('document_12th_marksheet')->nullable();
            $table->string('document_consolidated_marksheet')->nullable();
            $table->string('document_course_completion_certificate')->nullable();
            $table->string('document_degree_certificate')->nullable();
            $table->string('document_provisional_certificate')->nullable();
            $table->string('document_tc')->nullable();
            $table->string('document_aadhaar_card')->nullable();
            $table->string('document_pan_card')->nullable();
            $table->string('document_voter_id')->nullable();
            $table->string('document_driving_licence')->nullable();
            $table->string('document_experience_certificate')->nullable();
            $table->string('document_salary_slips')->nullable();
            $table->string('document_bank_passbook')->nullable();
            $table->string('signature_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intern_documents');
    }
};
