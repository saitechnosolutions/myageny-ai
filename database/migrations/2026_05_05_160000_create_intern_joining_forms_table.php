<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intern_joining_forms', function (Blueprint $table) {
            $table->id();
            $table->string('photograph')->nullable();
            $table->string('name');
            $table->string('father_name');
            $table->text('correspondence_address');
            $table->text('permanent_address');
            $table->string('mobile', 20);
            $table->string('email');
            $table->date('date_of_birth');
            $table->string('blood_group', 10)->nullable();
            $table->enum('marital_status', ['single', 'married'])->default('single');
            $table->date('date_of_marriage')->nullable();
            $table->string('aadhaar_card_no', 20);
            $table->string('pan_card_no', 20);
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_relation');
            $table->string('emergency_contact_no', 20);
            $table->boolean('declaration_accepted')->default(false);
            $table->date('declaration_date');
            $table->string('declaration_place');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intern_joining_forms');
    }
};
