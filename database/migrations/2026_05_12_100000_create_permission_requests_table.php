<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employee_onboardings')->nullOnDelete();
            $table->date('permission_date');
            $table->time('from_time');
            $table->time('to_time');
            $table->unsignedInteger('total_minutes')->default(0);
            $table->text('reason');
            $table->string('status', 30)->default('pending');
            $table->string('current_step', 80)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['current_step', 'status']);
            $table->index('permission_date');
        });

        Schema::create('permission_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_request_id')->constrained('permission_requests')->cascadeOnDelete();
            $table->unsignedTinyInteger('step_order');
            $table->string('step_key', 80);
            $table->string('step_name', 120);
            $table->foreignId('approver_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 30)->default('pending');
            $table->foreignId('actioned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('actioned_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['permission_request_id', 'step_key']);
            $table->index(['approver_user_id', 'status']);
            $table->index(['actioned_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_approvals');
        Schema::dropIfExists('permission_requests');
    }
};
