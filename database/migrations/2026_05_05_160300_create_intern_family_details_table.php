<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intern_family_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_joining_form_id')->constrained('intern_joining_forms')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('relation')->nullable();
            $table->string('occupation')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('mobile_no', 20)->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intern_family_details');
    }
};
