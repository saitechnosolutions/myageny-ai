<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_onboardings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->text('correspondence_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('mobile', 20);
            $table->string('email');
            $table->date('date_of_birth');
            $table->string('blood_group', 10)->nullable();
            $table->enum('marital_status', ['single', 'married'])->default('single');
            $table->date('date_of_marriage')->nullable();
            $table->string('aadhaar_card_no', 20)->nullable();
            $table->string('pan_card_no', 20)->nullable();
            $table->string('photograph')->nullable();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->string('emergency_contact_no', 20)->nullable();

            $table->string('reference_name')->nullable();
            $table->string('reference_organization_name')->nullable();
            $table->string('reference_designation')->nullable();
            $table->string('reference_contact_no', 20)->nullable();
            $table->string('reference_mail_id')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('bank_ifsc_code')->nullable();
            $table->string('bank_branch')->nullable();

            $table->date('declaration_date')->nullable();
            $table->string('declaration_place')->nullable();
            $table->string('signature')->nullable();

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

            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_onboardings');
    }
};
