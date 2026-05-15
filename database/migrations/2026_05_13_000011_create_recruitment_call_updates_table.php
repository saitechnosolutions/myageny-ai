<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_call_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('recruitment_candidate_id')->constrained('recruitment_candidates')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('called_at');
            $table->string('call_type', 30)->default('outgoing');
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('outcome', 50);
            $table->text('notes')->nullable();
            $table->dateTime('next_follow_up_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'called_at']);
            $table->index(['recruitment_candidate_id', 'called_at'], 'rec_call_candidate_called_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_call_updates');
    }
};
