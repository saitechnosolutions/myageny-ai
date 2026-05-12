<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_entries', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name', 150);
            $table->string('mobile_number', 30);
            $table->date('visit_date')->index();
            $table->time('in_time');
            $table->time('out_time')->nullable();
            $table->string('person_to_meet', 150);
            $table->string('status', 30)->default('checked_in')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['mobile_number', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_entries');
    }
};
