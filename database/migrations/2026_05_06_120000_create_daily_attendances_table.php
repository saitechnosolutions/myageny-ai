<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->index();
            $table->string('employee_name')->nullable();
            $table->string('attendance_photo');
            $table->text('login_location')->nullable();
            $table->decimal('login_latitude', 10, 7);
            $table->decimal('login_longitude', 10, 7);
            $table->time('login_time');
            $table->text('logout_location')->nullable();
            $table->decimal('logout_latitude', 10, 7)->nullable();
            $table->decimal('logout_longitude', 10, 7)->nullable();
            $table->time('logout_time')->nullable();
            $table->string('overall_working_hours', 20)->nullable();
            $table->date('attendance_date')->index();
            $table->string('attendance_status')->default('present');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date'], 'daily_attendance_employee_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_attendances');
    }
};
