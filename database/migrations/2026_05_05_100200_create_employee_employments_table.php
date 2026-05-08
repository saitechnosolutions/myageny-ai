<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_onboarding_id')->constrained('employee_onboardings')->cascadeOnDelete();
            $table->string('organisation')->nullable();
            $table->string('designation')->nullable();
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->string('annual_ctc', 50)->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_employments');
    }
};
