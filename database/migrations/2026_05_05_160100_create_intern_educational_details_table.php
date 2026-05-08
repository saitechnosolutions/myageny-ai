<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intern_educational_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_joining_form_id')->constrained('intern_joining_forms')->cascadeOnDelete();
            $table->string('qualification');
            $table->string('institution_name');
            $table->string('year_of_passing', 20);
            $table->string('percentage', 20);
            $table->string('specialization')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intern_educational_details');
    }
};
