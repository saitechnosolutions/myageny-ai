<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('candidate_no', 30)->unique();
            $table->string('name');
            $table->string('mobile_number', 30);
            $table->string('email')->nullable();
            $table->string('location')->nullable();
            $table->string('job_title');
            $table->string('source')->nullable();
            $table->decimal('current_ctc', 12, 2)->nullable();
            $table->decimal('expected_ctc', 12, 2)->nullable();
            $table->string('notice_period')->nullable();
            $table->unsignedTinyInteger('experience_years')->nullable();
            $table->string('resume_path')->nullable();
            $table->string('status', 30)->default('applied')->index();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index('job_title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_candidates');
    }
};
