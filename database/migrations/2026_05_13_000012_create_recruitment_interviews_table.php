<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('recruitment_candidate_id')->constrained('recruitment_candidates')->cascadeOnDelete();
            $table->foreignId('scheduled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('scheduled_at');
            $table->string('round', 80)->nullable();
            $table->string('mode', 30)->default('phone');
            $table->string('interviewer_name')->nullable();
            $table->string('interview_link')->nullable();
            $table->string('status', 30)->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'scheduled_at']);
            $table->index(['recruitment_candidate_id', 'scheduled_at'], 'rec_interview_candidate_scheduled_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_interviews');
    }
};
